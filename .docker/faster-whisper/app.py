from flask import Flask, abort, request
from flask_cors import CORS
from tempfile import NamedTemporaryFile
# https://github.com/guillaumekln/faster-whisper#usage

from faster_whisper import WhisperModel

import torch


# Check if NVIDIA GPU is available
torch.cuda.is_available()
DEVICE = "cuda" if torch.cuda.is_available() else "cpu"

model_size = "large-v2"
model_size = "base"

# Run on GPU with FP16
model = WhisperModel(model_size, device=DEVICE, compute_type="float16")

# or run on GPU with INT8
# model = WhisperModel(model_size, device="cuda", compute_type="int8_float16")
# or run on CPU with INT8
# model = WhisperModel(model_size, device="cpu", compute_type="int8")

app = Flask(__name__)
CORS(app)

@app.route("/")
def hello():
    return "Faster Whisper Hello World!"


@app.route('/whisper', methods=['POST'])
def handler():
    if not request.files:
        # If the user didn't submit any files, return a 400 (Bad Request) error.
        abort(400)

    # For each file, let's store the results in a list of dictionaries.
    results = []

    # Loop over every file that the user submitted.
    for filename, handle in request.files.items():
        # Create a temporary file.
        # The location of the temporary file is available in `temp.name`.
        temp = NamedTemporaryFile()
        # Write the user's uploaded file to the temporary file.
        # The file will get deleted when it drops out of scope.
        handle.save(temp)
        # Let's get the transcript of the temporary file.
        segments, info = model.transcribe(temp, beam_size=5)
        # Now we can store the result object for this file.
        results.append({
            'filename': filename,
            'info': info,
            'transcript': list(segments)
        })

    # This will be automatically converted to JSON.
    return {'results': results}

// https://web.dev/media-recording-audio/#save-the-data-from-the-microphone
// https://github.com/chrisguttandin/extendable-media-recorder

import { MediaRecorder, register } from 'extendable-media-recorder';
import { connect } from 'extendable-media-recorder-wav-encoder';

await register(await connect());

document.querySelectorAll('[data-transcribe-config]').forEach((el) => {
    const config = JSON.parse(el.dataset.transcribeConfig);
    const startButton = el.querySelector('[data-record-action="start"]');
    const stopButton = el.querySelector('[data-record-action="stop"]');
    const player = el.querySelector('audio');
    const downloadLink = el.querySelector('a');

    const messageElement = el.querySelector(config.transcribe_target);

    const handleSuccess = function (stream) {
        const options = { mimeType: 'audio/wav' };
        const recordedChunks = [];
        const mediaRecorder = new MediaRecorder(stream, options);

        mediaRecorder.start();

        mediaRecorder.addEventListener('dataavailable', function (e) {
            if (e.data.size > 0) recordedChunks.push(e.data);
        });

        mediaRecorder.addEventListener('stop', async function () {
            const file = new Blob(recordedChunks);

            player.src = URL.createObjectURL(file);

            downloadLink.href = URL.createObjectURL(file);
            downloadLink.download = messageElement.getAttribute('name') + '.wav';

            // Upload file to transcribe API
            try {
                const transcribeUrl = config.transcribe_url;

                const formData = new FormData();
                formData.set('audio_file', file);
                const response = await fetch(transcribeUrl, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log({ result });
                messageElement.value = result.content;
            } catch (error) {
                console.error({ error });
            }
        });

        stopButton.addEventListener('click', function () {
            mediaRecorder.stop();
        });
    };

    startButton.addEventListener('click', function () {
        navigator.mediaDevices.getUserMedia({ audio: true, video: false })
            .then(handleSuccess);
    });
});

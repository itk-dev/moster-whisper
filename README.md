# Moster Whisper

<https://github.com/guillaumekln/faster-whisper>

```sh
docker compose up --detach --build
```

## <https://github.com/lablab-ai/whisper-api-flask>

```sh
open "http://$(docker compose port whisper 5000)"
```

### Example

```sh
curl "http://$(docker compose port whisper 5000)/whisper" \
  --header 'content-type: multipart/form-data' \
  --form 'audio_file=@tests/test.da.wav;type=audio/x-wav' \
  --verbose
```

## <https://github.com/ahmetoner/whisper-asr-webservice/>

```sh
open "http://$(docker compose port openai-whisper-asr-webservice 9000)/docs"
```

**Note**: it may take a while for this to respond for the first time.

### Example

```sh
curl "http://$(docker compose port openai-whisper-asr-webservice 9000)/asr?task=transcribe&encode=true&output=txt" \
  --header 'content-type: multipart/form-data' \
  --form 'audio_file=@tests/test.da.wav;type=audio/x-wav' \
  --verbose
```

## Coding standards

```sh
docker compose run --rm node yarn install
docker compose run --rm node yarn coding-standards-check
```

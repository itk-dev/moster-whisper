# Moster Whisper

<https://github.com/guillaumekln/faster-whisper>

```sh
docker compose up --detach --build
docker compose exec phpfpm composer install
docker compose run --rm node yarn install
docker compose run --rm node yarn build
# We need HTTPS to record audio. This assumes we're using https://github.com/itk-dev/devops_itkdev-docker.
open "https://moster-whisper.local.itkdev.dk/record"
```

```sh
curl "http://$(docker compose port nginx 8080)/transcribe" \
  --header 'content-type: multipart/form-data' \
  --form 'audio_file=@tests/test.da.wav;type=audio/x-wav' \
  --verbose
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
  --header 'accept: application/json' \
  --header 'content-type: multipart/form-data' \
  --form 'audio_file=@tests/test.da.wav;type=audio/x-wav' \
  --verbose
```

## Coding standards

```sh
docker compose exec phpfpm composer coding-standards-check
docker compose exec phpfpm composer coding-standards-apply
```

```sh
docker compose run --rm node yarn install
docker compose run --rm node yarn coding-standards-check
docker compose run --rm node yarn coding-standards-apply
```

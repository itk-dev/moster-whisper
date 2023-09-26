<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/transcribe', name: 'transcribe_')]
class TranscribeController extends AbstractController
{
    #[Route('', name: 'transcribe', methods: ['POST'])]
    public function index(Request $request, HttpClientInterface $client): JsonResponse
    {
        try {
            /** @var UploadedFile $file */
            $file = $request->files->get('audio_file');

            if (null === $file) {
                return $this
                    ->json([
                        'error' => 'Please POST a file (audio_file)',
                    ])
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $tempFile = tempnam(sys_get_temp_dir(), $file->getFilename());

            if ($request->query->get('debug')) {
                return $this->json([
                    'content' => json_encode([
                        'filename' => $file->getClientOriginalName(),
                        'mime-type' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                    ]),
                ]);
            }

            $formDataPart = new FormDataPart([
                'audio_file' => new DataPart($file->getContent(), $file->getClientOriginalName(), $file->getClientMimeType()),
            ]);
            $response = $client->request('POST', 'http://openai-whisper-asr-webservice:9000/asr?task=transcribe&encode=true&output=txt', [
                'headers' => $formDataPart->getPreparedHeaders()->toArray(),
                'body' => $formDataPart->bodyToString(),
            ]);

            return $this->json([
                'content' => $response->getContent(false),
            ]);

            return $this->json([
                'message' => 'Welcome to your new controller!',
                'path' => 'src/Controller/TranscribeController.php',
            ]);
        } catch (\Throwable $exception) {
            return $this
                ->json([
                    'error' => $exception->getMessage(),
                ])
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        } finally {
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}

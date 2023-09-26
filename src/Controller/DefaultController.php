<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'transcribe_url' => $this->generateUrl('transcribe_transcribe'),
        ]);
    }

    #[Route('/record', name: 'record')]
    public function record(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            $data = $request->request->all();
            $data = array_map(static fn ($value) => is_string($value) ? str_replace("\r\n", "\n", $value) : $value, $data);

            return new Response(
                Yaml::dump($data, flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_NULL_AS_TILDE),
                Response::HTTP_OK,
                [
                    'content-type' => 'text/plain',
                ]
            );
        }

        return $this->render('default/record.html.twig', [
            'transcribe_url' => $this->generateUrl('transcribe_transcribe', [
                'debug' => $request->query->get('debug'),
            ]),
        ]);
    }
}

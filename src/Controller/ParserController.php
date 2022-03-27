<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\EvilLinks\EvilLinksGrabberInterface;
use App\Services\EvilLinks\StoreLinks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;

class ParserController extends AbstractController
{
    protected EvilLinksGrabberInterface $grabber;
    protected StoreLinks $storeLinks;

    public function __construct(
        EvilLinksGrabberInterface $evilLinksGrabber,
        StoreLinks $storeLinks
    ) {
        $this->grabber = $evilLinksGrabber;
        $this->storeLinks = $storeLinks;
    }

    #[Route("/parser/test", name: "parser_test", methods: ["GET", "OPTIONS", "HEAD"])]
    public function index(): JsonResponse
    {
        $serializer = new Serializer(
            [
                new GetSetMethodNormalizer(),
                new DateTimeNormalizer(),
                new UidNormalizer()
            ],
            [
                new JsonEncoder()
            ]
        );

        $links = $this->grabber->execute();
        $this->storeLinks->store($links);

        $links = $serializer->normalize($links);

        return new JsonResponse([
            'success' => true,
            'code' => JsonResponse::HTTP_OK,
            'data' => $links
        ]);
    }
}

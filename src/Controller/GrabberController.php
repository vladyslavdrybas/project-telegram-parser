<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\ChannelGrabber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GrabberController extends AbstractController
{
    protected ChannelGrabber $grabber;

    public function __construct(ChannelGrabber $grabber) {
        $this->grabber = $grabber;
    }

    #[Route("/grab/telegram/posts", name: "grab_telegram_posts", methods: ["GET", "OPTIONS", "HEAD"])]
    public function grabTelegramPosts(): JsonResponse
    {
        return $this->json($this->grabber->execute());
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

class SocialMediaLinkMinerPost implements MinerPostServiceInterface
{
    public function execute(array $miner, array $post): void
    {
        dump($miner);
        dump($post);
        exit;
    }
}

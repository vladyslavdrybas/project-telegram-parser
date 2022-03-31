<?php

declare(strict_types=1);

namespace App\Services;

interface MinerPostServiceInterface
{
    public function execute(array $miner, array $post): void;
}

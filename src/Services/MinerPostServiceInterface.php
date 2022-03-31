<?php

declare(strict_types=1);

namespace App\Services;

use App\Transfer\MinerTransfer;
use App\Transfer\PostTransfer;

interface MinerPostServiceInterface
{
    public function execute(MinerTransfer $miner, PostTransfer $post): void;
}

<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;

use App\Transfer\TelegramPostTransfer;

interface PostTransformerInterface
{
    public function transform(TelegramPostTransfer $transfer): bool;
}

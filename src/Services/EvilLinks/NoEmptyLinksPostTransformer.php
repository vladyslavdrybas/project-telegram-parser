<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;

use App\Transfer\TelegramPostTransfer;

class NoEmptyLinksPostTransformer implements PostTransformerInterface
{
    public function transform(TelegramPostTransfer $transfer): bool
    {
        return count($transfer->getLinks()) !== 0;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;

use App\Transfer\TelegramPostTransfer;
use function array_filter;
use function json_decode;
use function str_contains;

class CanHaveLinksPostTransformer implements PostTransformerInterface
{
    protected array $whitelist;

    public function __construct(string $whitelist)
    {
        $this->whitelist = json_decode($whitelist);
    }

    public function transform(TelegramPostTransfer $transfer): bool
    {
        $links = $transfer->getLinks();
        $links = array_filter(
            $links,
            function ($link) {
                foreach ($this->whitelist as $whiteLink) {
                    if (str_contains($link, $whiteLink)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $transfer->setLinks($links);

        return true;
    }
}

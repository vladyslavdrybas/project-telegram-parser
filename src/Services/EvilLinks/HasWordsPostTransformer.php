<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;

use App\Transfer\TelegramPostTransfer;
use function json_decode;
use function str_contains;

class HasWordsPostTransformer implements PostTransformerInterface
{
    protected array $words;

    public function __construct(string $words)
    {
        $this->words = json_decode($words);
    }

    public function transform(TelegramPostTransfer $transfer): bool
    {
        foreach ($this->words as $word) {
            if (str_contains($transfer->getPostHtml(), $word)) {
                $transfer->setWordTrigger($word);

                return true;
            }
        }

        return false;
    }
}

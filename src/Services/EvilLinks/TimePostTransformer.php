<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;

use App\Transfer\TelegramPostTransfer;
use Symfony\Component\DomCrawler\Crawler;

class TimePostTransformer implements PostTransformerInterface
{
    public function transform(TelegramPostTransfer $transfer): bool
    {
        $crawler = new Crawler($transfer->getPostHtml());
        $datetime = $crawler->filter('time')->each(function (Crawler $node) {
            return $node->attr('datetime');
        });

        if (count($datetime) !== 1) {
            return false;
        }

        $transfer->setDatetime($datetime[0]);

        return true;
    }
}
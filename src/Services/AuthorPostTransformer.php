<?php

declare(strict_types=1);

namespace App\Services;

use App\Transfer\TelegramPostTransfer;
use Symfony\Component\DomCrawler\Crawler;
use function count;

class AuthorPostTransformer implements PostTransformerInterface
{
    public function transform(TelegramPostTransfer $transfer): bool
    {
        $crawler = new Crawler($transfer->getPostHtml());
        $owner = $crawler->filter('.tgme_widget_message_author > a.tgme_widget_message_owner_name')->each(function (Crawler $node) {
            return [$node->attr('href'), $node->text()];
        });

        if (count($owner) !== 1) {
            return false;
        }

        $transfer->setAuthorLink($owner[0][0]);
        $transfer->setAuthorName($owner[0][1]);

        return true;
    }
}

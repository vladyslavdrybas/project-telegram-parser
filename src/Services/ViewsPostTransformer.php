<?php

declare(strict_types=1);

namespace App\Services;

use App\Transfer\TelegramPostTransfer;
use Symfony\Component\DomCrawler\Crawler;
use function count;
use function str_contains;
use function str_replace;

class ViewsPostTransformer implements PostTransformerInterface
{
    public function transform(TelegramPostTransfer $transfer): bool
    {
        $crawler = new Crawler($transfer->getPostHtml());
        $views = $crawler->filter('.tgme_widget_message_info > .tgme_widget_message_views')->each(function (Crawler $node) {
            return $node->text();
        });

        if (count($views) === 0) {
            return true;
        }

        $views = $views[0];
        if (str_contains($views, 'K')) {
            $views = str_replace('K', '', $views);
            $views *= 1000;
        } elseif (str_contains($views, 'M')) {
            $views = str_replace('M', '', $views);
            $views *= 1000000;
        }

        $transfer->setViews((int) $views);

        return true;
    }
}

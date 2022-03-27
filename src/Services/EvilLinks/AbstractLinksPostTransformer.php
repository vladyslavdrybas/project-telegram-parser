<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;

use App\Transfer\TelegramPostTransfer;
use Symfony\Component\DomCrawler\Crawler;
use function array_filter;

abstract class AbstractLinksPostTransformer implements PostTransformerInterface
{
    protected const LINK = 'https://t.me';

    public function transform(TelegramPostTransfer $transfer): bool
    {
        $crawler = new Crawler($transfer->getPostHtml());
        $links = $crawler->filter('a')->each(function (Crawler $node) {
            return $node->attr('href');
        });

        $links = array_filter(
            $links,
            function ($link) {
                return str_starts_with($link, static::LINK);
            }
        );

        foreach ($links as $link) {
            $transfer->addLink($link);
        }

        return true;
    }
}

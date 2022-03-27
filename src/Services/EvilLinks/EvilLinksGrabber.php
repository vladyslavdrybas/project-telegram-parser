<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;

use App\Transfer\LinkTransfer;
use App\Transfer\TelegramPostTransfer;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_key_exists;
use function array_shift;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function set_time_limit;
use function str_contains;
use function str_replace;
use function strpos;
use function substr;
use const JSON_ERROR_NONE;

class EvilLinksGrabber implements EvilLinksGrabberInterface
{
    protected HttpClientInterface $client;
    protected LoggerInterface $logger;
    protected ParameterBagInterface $parameterBag;
    protected array $sources;
    /**
     * @var \App\Services\EvilLinks\PostTransformerInterface[]
     */
    protected array $transformers;

    public function __construct(
        LoggerInterface $logger,
        ParameterBagInterface $parameterBag,
        HttpClientInterface $client,
        string $sources,
        array $transformers
    ) {
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
        $this->client = $client;
        $this->sources = json_decode($sources);;
        $this->transformers = $transformers;
    }

    public function execute(): array
    {
        $links = $this->getLinksFromSources($this->sources);

        return $links;
    }

    protected function getLinksFromSources(array $sources): array
    {
        $links = [];

        do {
            set_time_limit(180);
            $source = array_shift($sources);

            if (empty($source)) {
                break;
            }

            if (!str_contains($source, 'https://t.me')) {
                continue;
            }

            $response = $this->client->request(
                'POST',
                $source
            );

            if ($response->getStatusCode() >= Response::HTTP_BAD_REQUEST
                || $response->getStatusCode() < Response::HTTP_OK
            ) {
                continue;
            }

            try {
                $html = $response->getContent();

                if (empty($html)) {
                    continue;
                }

                $crawler = new Crawler($html);
                $crawlerBefore = $crawler->filter('.tme_messages_more');
                if ($crawlerBefore->count() > 0) {
                    $idBefore = $crawlerBefore->attr('data-before');
                    if (!empty($idBefore)) {
                        $channel = str_replace('https://t.me/s/', '', $source);
                        if (str_contains($channel, '?')) {
                            $channel = substr($channel,0 , strpos($channel, '?'));
                        }

                        $idBefore = (int) $idBefore;
                        do {
                            $parsed = $this->isParsed($channel, $idBefore);
                            if ($parsed === false) {
                                $this->logger->info('parse older source', ['https://t.me/s/' . $channel . '?before=' . $idBefore]);
                                $sources[] = 'https://t.me/s/' . $channel . '?before=' . $idBefore;
                            } else {
                                $idBefore -= 30;
                            }
                        } while ($parsed !== false && $idBefore > 0);
                    }
                }

                $telegramPosts = $this->getTelegramPosts($crawler);

                foreach ($telegramPosts as $post) {
                    if (!$post instanceof TelegramPostTransfer) {
                        continue;
                    }

                    foreach ($post->getLinks() as $link) {
                        $links[] = new LinkTransfer(
                            $post->getAuthorName(),
                            $post->getAuthorLink(),
                            $post->getPostId(),
                            $link,
                            $post->getDatetime(),
                            $post->getWordTrigger(),
                            $post->getViews()
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
        } while (true);

        return $links;
    }

    protected function getTelegramPosts(Crawler $crawler): array
    {
        $crawler = $crawler->filter('.tgme_widget_message_wrap');
        if ($crawler->count() === 0) {
            return [];
        }

        /** @var TelegramPostTransfer[] $telegramPosts */
        $telegramPosts = $crawler->each(function (Crawler $node, $i) {
            return new TelegramPostTransfer(
                $node->filter('.tgme_widget_message')->attr('data-post'),
                $node->html()
            );
        });

        foreach ($telegramPosts as $key => $post) {
            foreach ($this->transformers as $filter) {
                if (empty($post->getPostId())
                    || empty($post->getPostHtml())
                    || !$filter->transform($post)
                ) {
                    unset($telegramPosts[$key]);
                }
            }
        }

        return $telegramPosts;
    }

    protected function isParsed(string $channel, int $lastId): bool
    {
        set_time_limit(20);
        $response = $this->client->request(
            'GET',
            $this->parameterBag->get('store_link_api') . '/evil-channel/' . $channel . '/' . $lastId . '?apikey=' . $this->parameterBag->get('store_link_api_token'),
        );

        try {
            $content = $response->getContent();
            $content = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(json_last_error_msg());
            }

            return array_key_exists('success', $content) && $content['success'] === true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }
}

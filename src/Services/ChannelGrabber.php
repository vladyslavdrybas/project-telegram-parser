<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use function array_key_exists;
use function base64_encode;

class ChannelGrabber extends AbstractService
{
    public function execute(): bool
    {
        $leoinfohub = $this->parameterBag->get('store_link_api');
        $token = $this->parameterBag->get('store_link_api_token');

        if (empty($leoinfohub)
            || empty($token)
        ) {
            $this->logger->error('Env variables not found');

            return false;
        }

        try {
            $response = $this->client->request(
                'GET',
                $leoinfohub . '/channel/list?apikey=' . $token
            );

            $channels = $response->toArray(false);

            foreach ($channels as $channel) {
                if (!array_key_exists('source', $channel)) {
                    continue;
                }

                if (!array_key_exists('title', $channel['source'])) {
                    continue;
                }

                if ($channel['source']['title'] !== 'telegram') {
                    continue;
                }

                $response = $this->client->request(
                    'GET',
                    $leoinfohub . '/post/last/' . $channel['title'] . '?apikey=' . $token
                );

                $post = $response->toArray(false);
                $postNumber = 1;
                if (array_key_exists('postNumber', $post)) {
                    $postNumber = $post['postNumber'] + 1;
                }

                $postTry = 0;
                do {
                    $postNumber += $postTry;
                    $source = $channel['messageLink'] . '/' . $postNumber;
                    $response = $this->client->request(
                        'GET',
                        $source
                    );

                    $html = $response->getContent(false);
                    $crawler = new Crawler($html);
                    $telegramPost = $crawler->filter('.tgme_widget_message[data-post="' . $channel['title'] . '/' . $postNumber . '"]');
                    $postTry++;
                } while ($telegramPost->count() === 0 && $postTry < 8);

                if ($telegramPost->count() === 0) {
                    continue;
                }

                $this->client->request(
                    'POST',
                    $leoinfohub . '/post?apikey=' . $token,
                    [
                        'json' => [
                            'channelTitle' => $channel['title'],
                            'postNumber' => $postNumber,
                            'meta' => base64_encode($telegramPost->html()),
                        ],
                    ]
                );
            }
        } catch (\Exception $e) {
            $this->logger->error(
                $e->getMessage()
            );

            return false;
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_key_exists;
use function base64_encode;

class ChannelGrabber
{
    protected HttpClientInterface $client;
    protected LoggerInterface $logger;
    protected ParameterBagInterface $parameterBag;
    protected SerializerInterface $serializer;
    protected NormalizerInterface $normalizer;

    public function __construct(
        HttpClientInterface $client,
        LoggerInterface $logger,
        ParameterBagInterface $parameterBag,
        SerializerInterface $serializer,
        NormalizerInterface $normalizer
    ) {
        $this->logger = $logger;
        $this->client = $client;
        $this->parameterBag = $parameterBag;
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
    }

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
                            'channel' => $channel['title'],
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

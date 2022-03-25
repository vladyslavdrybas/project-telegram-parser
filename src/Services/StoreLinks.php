<?php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function set_time_limit;

class StoreLinks
{
    protected LoggerInterface $logger;
    protected HttpClientInterface $client;
    protected ParameterBagInterface $parameterBag;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $client,
        ParameterBagInterface $parameterBag
    ) {
        $this->logger = $logger;
        $this->client = $client;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param \App\Transfer\LinkTransfer[] $links
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function store(array $links): void
    {
        $serializer = new Serializer(
            [
                new GetSetMethodNormalizer(),
                new DateTimeNormalizer(),
                new UidNormalizer()
            ],
            [
                new JsonEncoder()
            ]
        );

        foreach ($links as $link) {
            try {
                set_time_limit(10);

                $data = $serializer->serialize($link, 'json');

                $request = [
                    'platform' => $link->getPlatform(),
                    'link' => $link->getLink(),
                    'postId' => $link->getPostId(),
                    'createdAt' => $link->getDatetime(),
                    'meta' => $data,
                ];

                $response = $this->client->request(
                    'POST',
                    $this->parameterBag->get('store_link_api') . '/evil-channel?apikey=' . $this->parameterBag->get('store_link_api_token'),
                    [
                        'json' => $request,
                    ]
                );
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), [$response->getContent(false)]);

                continue;
            }
        }
    }
}

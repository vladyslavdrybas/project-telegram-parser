<?php

declare(strict_types=1);

namespace App\Services;

use function array_intersect;
use function array_keys;

class MinerQueueBuilder extends AbstractService
{
    protected const KEYS = [
      'minerTitle',
      'channelTitle',
      'postNumber',
    ];

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
                $leoinfohub . '/miner/channel/posts/connect?apikey=' . $token
            );
            $items = $response->toArray(false);

            foreach ($items as $item) {
                if (count(array_intersect(static::KEYS, array_keys($item))) !== count(static::KEYS)) {
                    $this->logger->error(
                        'cannot find keys to add post to miner',
                        $item
                    );
                    continue;
                }

                $response = $this->client->request(
                    'POST',
                    $leoinfohub . '/miner/post/connect?apikey=' . $token,
                    [
                        'json' => [
                            'minerTitle' => $item['minerTitle'],
                            'channelTitle' => $item['channelTitle'],
                            'postNumber' => $item['postNumber'],
                        ],
                    ]
                );

                if (count($response->toArray(false)) === 0) {
                    $this->logger->error(
                        'cannot store post to miner',
                        $item
                    );
                };
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

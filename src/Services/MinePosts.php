<?php

declare(strict_types=1);

namespace App\Services;

use App\Transfer\MinerTransfer;
use App\Transfer\PostTransfer;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_key_exists;

class MinePosts extends AbstractService
{
    protected array $minerServices;
    protected DenormalizerInterface $denormalizer;

    public function __construct(
        HttpClientInterface $client,
        LoggerInterface $logger,
        ParameterBagInterface $parameterBag,
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer,
        array $minerServices
    ) {
        parent::__construct($client, $logger, $parameterBag, $serializer, $normalizer);
        $this->minerServices = $minerServices;
        $this->denormalizer = $denormalizer;
    }

    public function execute(string $minerTitle): bool
    {
        if (!array_key_exists($minerTitle, $this->minerServices)) {
            $this->logger->error('Miner does not set.');

            return false;
        }

        $minerService = $this->minerServices[$minerTitle];
        if (!$minerService instanceof MinerPostServiceInterface) {
            $this->logger->error('Miner service does not implement service.');

            return false;
        }

        $leoinfohub = $this->parameterBag->get('store_link_api');
        $token = $this->parameterBag->get('store_link_api_token');

        if (empty($leoinfohub)
            || empty($token)
        ) {
            $this->logger->error('Env variables not found.');

            return false;
        }

        try {
            $response = $this->client->request(
                'GET',
                $leoinfohub . '/miner/queue/' . $minerTitle . '?apikey=' . $token
            );
            $data = $response->toArray(false);

            if (!array_key_exists('miner', $data)) {
                $this->logger->error('Array key "miner" not found.');

                return false;
            }

            if (!array_key_exists('posts', $data)) {
                $this->logger->error('Array key "posts" not found.');

                return false;
            }

            $miner = $data['miner'];
            $miner = $this->denormalizer->denormalize($miner, MinerTransfer::class);
            foreach ($data['posts'] as $post) {
                $post = $this->denormalizer->denormalize($post, PostTransfer::class);
                $minerService->execute($miner, $post);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }

        return true;
    }
}

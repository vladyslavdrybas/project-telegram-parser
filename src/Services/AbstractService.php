<?php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractService
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
}

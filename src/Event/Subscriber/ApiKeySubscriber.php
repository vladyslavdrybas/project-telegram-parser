<?php

namespace App\Event\Subscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function trim;

final class ApiKeySubscriber implements EventSubscriberInterface
{
    protected const EXCEPTION_ACCESS_DENIED_MESSAGE = 'Access Denied';
    protected const API_KEY_NAME = 'apikey';

    /**
     * @var array
     */
    protected array $accessTokens = [];

    /**
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->accessTokens = explode(',', $parameterBag->get('access_tokens'));
    }

    /**
     * @param RequestEvent $requestEvent
     */
    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();
        $token = '';

        if ($request->query->has(self::API_KEY_NAME)) {
            $token = trim($request->query->get(self::API_KEY_NAME));
        } else if ($request->headers->has(self::API_KEY_NAME)) {
            $token = trim($request->headers->get(self::API_KEY_NAME));
        }

        if (!empty($token) && \in_array($token, $this->accessTokens, true)) {
            return;
        }

        $this->accessDeniedException();
    }

    /**
     * @return void
     */
    protected function accessDeniedException(): void
    {
        throw new AccessDeniedException(self::EXCEPTION_ACCESS_DENIED_MESSAGE);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}

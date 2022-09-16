<?php

namespace App\Subscriber;

use App\Exception\ApiInvalidRequestData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Normalizer\FormErrorNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionRequestDataSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly SerializerInterface $serializer) {}

    public static function getSubscribedEvents():array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event):void
    {
        $exception = $event->getThrowable();

        if (false === $exception instanceof ApiInvalidRequestData) {
            return;
        }

        $responseData = $exception->getForm();

        $responseData = json_decode($this->serializer->serialize($responseData, 'json', [FormErrorNormalizer::CODE => $exception->getCode(), FormErrorNormalizer::TITLE => $exception->getMessage()]), true);
        $response = new JsonResponse($responseData, $exception->getCode());

        $event->setResponse($response);
    }
}

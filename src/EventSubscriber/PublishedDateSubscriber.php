<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\PublishedDateEntityInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PublishedDateSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            KernelEvents::VIEW => [
                'setPublishedDate', EventPriorities::PRE_WRITE
            ]
        ];
    }

    public function setPublishedDate(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$entity instanceof PublishedDateEntityInterface || !in_array($method, [Request::METHOD_PUT, Request::METHOD_POST])) {
            return;
        }

        $entity->setPublished(new \DateTime());
    }
}

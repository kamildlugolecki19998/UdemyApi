<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AuthoredEntityInterface;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthoredEntitySubscriber implements EventSubscriberInterface
{

    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.

        return [
            KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE]
        ];
    }

    public function getAuthenticatedUser(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$entity instanceof AuthoredEntityInterface || Request::METHOD_POST !== $method){
            return;
        }

        /** @var User $author */
        $author = $this->tokenStorage->getToken()->getUser();

        $entity->setAuthor($author);
    }
}

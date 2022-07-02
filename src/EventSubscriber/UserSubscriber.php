<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /** @var UserPasswordHasherInterface */
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.

        return
            [
                KernelEvents::VIEW => ['hashUserPassword', EventPriorities::PRE_WRITE]
            ];
    }

    public function hashUserPassword(ViewEvent $event)
    {
        $method = $event->getRequest()->getMethod();
        $entity = $event->getControllerResult();
//        var_dump($method);
//        var_dump(!$entity instanceof User || !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT]));

        if(!$entity instanceof User && !in_array($method, [Request::METHOD_POST])) {
            return;
        }

        $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPassword()));
    }
}

<?php

namespace App\Serializer;

use App\Entity\BlogPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface as NormalizerAwareInterfaceAlias;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BlogPostAttributeNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterfaceAlias
{
    use NormalizerAwareTrait;

    private TokenStorageInterface $tokenStorage;

    const BLOG_POST_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'BLOG_POST_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        if ($this->isBlogOwner($object)) {
            $context['groups'][] = 'blog_owner';
        }

        $context[self::BLOG_POST_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::BLOG_POST_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof BlogPost;
    }

    private function isBlogOwner($object): bool
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        return $object->getAuthor()->getEmail() === $user->getEmail();
    }
}

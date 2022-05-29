<?php

namespace App\Serializer;

use App\Entity\User;
use \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserAttributeNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        if ($this->userIsHimself($object)) {
            $context['groups'][] = 'get-owner';
        }

        return $this->passOn($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        // Make sure we're not called twice
        if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    private function userIsHimself(mixed $object)
    {
        return $object->getEmail() === $this->tokenStorage->getToken()->getUser()->getEmail();
    }

    private function passOn($object, $format, $context)
    {
        if (!$this->normalizer instanceof NormalizerInterface) {
            throw new \LogicException(
                sprintf('Connot normalizer object %s because inject serializer is not a normalizer', $object));
        }

        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }
}

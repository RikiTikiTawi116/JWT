<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadUserByUsername($email): User
    {
        $user = $this->findOneUserBy(['email' => $email]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with "%s" email does not exist.', $email));
        }

        return $user;
    }

    public function loadUserByIdentifier($id): User
    {
        $user = $this->findOneUserBy(['id' => $id]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with "%s" id does not exist.', $id));
        }

        return $user;
    }

    private function findOneUserBy(array $options): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy($options);
    }

    public function refreshUser(UserInterface $user): User
    {
        assert($user instanceof User);

        if (null === $reloadedUser = $this->findOneUserBy(['email' => $user->getEmail()])) {
            throw new UserNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}

<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateUserController extends AbstractController
{
    #[Route('/user/update', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request,
                                UserRepository $userRepository,
                                UserProvider $userProvider,
                                ValidatorInterface $validator,
                                UserPasswordHasherInterface $passwordHasher,
                                ): JsonResponse {
        $body = $this->transformJsonBody($request);

        $email = $body->get('email');

        $user = $userProvider->loadUserByUsername($email);

        $user->setEmail($email);

        if (!empty($body->get('firstName'))) {
            $user->setFirstName($body->get('firstName'));
        } else {
            $user->setFirstName($user->getFirstName('firstName'));
        }

        if (!empty($body->get('lastName'))) {
            $user->setLastName($body->get('lastName'));
        } else {
            $user->setLastName($user->getLastName('lastName'));
        }

        if (!empty($body->get('phone'))) {
            $user->setPhone($body->get('phone'));
        } else {
            $user->setPhone($user->getPhone('phone'));
        }

        if (!empty($body->get('firstName'))) {
            $user->setUsername($body->get('firstName'));
        } else {
            $user->setUsername($user->getFirstName('firstName'));
        }

        if (!empty($body->get('roles'))) {
            $user->setRoles($body->get('roles'));
        } else {
            $user->setRoles($user->getRoles('roles'));
        }

        if (!empty($body->get('password'))) {
            $pass = $body->get('password');
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $pass
            );
            $user->setPassword($hashedPassword);
        } else {
            $pass = $user->getPassword('password');
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $pass
            );
            $user->setPassword($hashedPassword);
        }

        $errors = $validator->validate($user);
        if (0 === count($errors)) {
            $data = 'User Updated!';
            $status = 200;
            $userRepository->save($user, true);
        } else {
            $errorsString = (string) $errors;
            $data = $errorsString;
            $status = 422;
        }

        return new JsonResponse($data, $status);
    }

    protected static function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}

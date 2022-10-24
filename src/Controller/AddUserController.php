<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddUserController extends AbstractController
{
    #[Route('/user/add', name: 'user_add', methods: ['GET', 'HEAD'])]
    #[IsGranted('ROLE_ADMIN')]
    public function addUser(Request $request,
                            UserRepository $userRepository,
                            ValidatorInterface $validator,
                            ): JsonResponse {
        $body = $this->transformJsonBody($request);

        $user = new User();

        $user->setEmail($body->get('email'));
        $user->setFirstName($body->get('firstName'));
        $user->setLastName($body->get('lastName'));
        $user->setPhone($body->get('phone'));
        $user->setRoles($body->get('roles'));
        $user->setUsername($body->get('firstName'));
        $user->setPassword($body->get('password'));

        $errors = $validator->validate($user);

        if (0 === count($errors)) {
            $userRepository->save($user, true);
            $data = 'User Added!';
            $status = 200;
        } else {
            $errorsString = (string) $errors;
            $data = $errorsString;
            $status = 422;
        }

        return new JsonResponse($data, $status);
    }

    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}

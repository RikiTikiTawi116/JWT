<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteUserController extends AbstractController
{
    #[Route('/user/delete', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(Request $request,
                                UserRepository $userRepository,
                                UserProvider $userProvider,
                                ): Response {
        try {
            $body = $this->transformJsonBody($request);
            $email = $body->get('email');

            $user = $userProvider->loadUserByUsername($email);

            $userRepository->remove($user, true);

            $data = 'User Deleted!';
            $status = 200;

            return new Response($data, 200);
        } catch (\Exception $e) {
            $data = 'Bad Request!';

            return new Response($data, 400);
        }
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

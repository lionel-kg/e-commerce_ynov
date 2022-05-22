<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends CustomAbstractController
{
    /**
     * @Route("/api/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->sendSuccess("login");

    }

    /**
     * @Route("/api/client/login_check" , name="api_login_client")
     * @return JsonResponse
     */
    public function api_login_client():JsonResponse
    {
        $user = $this->getUser();
        return $this->json(array(
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ));
    }

    /**
     * @Route("/api/admin/login_check" , name="api_login_admin")
     * @return JsonResponse
     */
    public function api_login_admin():JsonResponse
    {
        $user = $this->getUser();
        return $this->json(array(
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ));
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
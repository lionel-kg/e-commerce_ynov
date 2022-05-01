<?php

namespace App\Controller;

use App\Service\User as UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * @Route("/user", name="app_user")
 */
class UserController extends CustomAbstractController
{

    /**
     * @Route("/add", methods={"POST"} , name="add_user")
     * @param Request $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function add(Request $request, UserService $userService): JsonResponse
    {
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        $waitedParameters = [
            "email" => "string",
        ];
        ["error" => $error , "parameters" => $newParameters] = $this->checkParameters($parameters,$waitedParameters);
        if($error !== ""){
            return $this->sendError($error,$error);
        }
        [
            "error" => $error,
            "errorDebug" => $errorDebug,
            "user" => $user,
        ] = $userService->add($parameters);
        dd($user);
        return $this->sendSuccess("User created success",$user, response::HTTP_CREATED);
    }
}

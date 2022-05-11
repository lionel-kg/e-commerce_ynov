<?php

namespace App\Controller;

use App\Entity\Admin as AdminEntity;
use App\Service\User as UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/admin", name="app_admin")
 */
class AdminController extends CustomAbstractController
{
    /**
     * @Route("/add", methods={"POST"} , name="_add")
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
            "nom"=>"string",
            "prenom"=>"string",
            "pseudo"=> "string",
            "password"=>"string",
            "dateNaissance"=>"string",

        ];
        ["error" => $error , "parameters" => $newParameters] = $this->checkParameters($parameters,$waitedParameters);
        if($error !== ""){
            return $this->sendError($error,$error);
        }
        [
            "error" => $error,
            "errorDebug" => $errorDebug,
            "user" => $user,
        ] = $userService->add($newParameters,AdminEntity::class);
        return $this->sendSuccess("User created success",$user, response::HTTP_CREATED);
    }

    public function edit(Request $request,UserService $userService){
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        $waitedParameters = [
            "email"=>"string",
        ];
        ["error"=>$error,"parameters"=>$newParameters] = $this->checkParameters($parameters,$waitedParameters);
        if($error !== ""){
            return $this->sendError($error,$error);
        }
        [
            "error" => $error,
            "errorDebug"=> $errorDebug,
            "user"=> $user,
        ] = $userService->edit($parameters);
        return $this->sendSuccess("User Edited Success",$user,response::HTTP_CREATED);
    }
}

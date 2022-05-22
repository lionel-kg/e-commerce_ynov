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

    /**
     * @Route ("/edit", methods={"POST"}, name="_edit")
     * @param Request $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function edit(Request $request,UserService $userService): JsonResponse
    {
        $errorDebug = "";
        $jwt = $this->getJwt($request);
        $parameters = $this->getParameters($request);
        $waitedParameters = [
            "email_OPT" => "string",
            "nom_OPT"=>"string",
            "prenom_OPT"=>"string",
            "pseudo_OPT"=> "string",
            "dateNaissance_OPT"=>"string",

        ];
        ["error"=>$error,"parameters"=>$newParameters] = $this->checkParameters($parameters,$waitedParameters);
        if($error !== ""){
            return $this->sendError($error,$error);
        }
        [
            "error" => $error,
            "errorDebug"=> $errorDebug,
            "user"=> $user,
        ] = $userService->edit($newParameters,$jwt);
        return $this->sendSuccess("User Edited Success",$user,response::HTTP_CREATED);
    }

    /**
     * @Route("/active/{id}",methods={"POST"},name="_user_edit")
     * @param Request $request
     * @param UserService $userService
     * @param string $id
     * @return JsonResponse
     * @throws \JsonException
     */
    public function adminActiveUser(Request $request,UserService $userService,string $id){
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        $jwt = $this->getJwt($request);
        $waitedParameters = [
            "active_OPT" => "int",
        ];
        ["error"=>$error,"parameters"=>$newParameters] = $this->checkParameters($parameters,$waitedParameters);

        if ($error !== "") {
            return $this->sendError($error,$error);
        }
        [
            "error" => $error,
            "errorDebug"=> $errorDebug,
            "user"=> $user,
        ] = $userService->adminActiveUser($newParameters,$jwt,$id);
        return $this->sendSuccess("User Edited Success",$user,response::HTTP_CREATED);
    }

    /**
     * @Route("/user/all",methods={"GET"},name="_user_all")
     * @return void
     * @throws \JsonException
     */
    public function getAllUser(Request $request,UserService $userService):JsonResponse
    {
        $errorDebug = "";
        $jwt = $this->getJwt($request);
        [
            "error"=>$error,
            "errorDebug"=>$errorDebug,
            "users"=>$users,
        ] = $userService->getAllUser($jwt);
        if($errorDebug !== "")
        {
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("recover users success",$users,response::HTTP_CREATED);
    }
}

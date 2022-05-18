<?php

namespace App\Controller;

use App\Entity\Client as ClientEntity;
use App\Service\Commande as CommandeService;
use App\Service\User as UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client", name="app_client")
 */
class ClientController extends CustomAbstractController
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
        ] = $userService->add($newParameters,ClientEntity::class);
        if($errorDebug !== ""){
            return $this->sendError($error,$errorDebug);
        }
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
     * @Route("/commande",methods={"GET"},name="")
     * @param Request $request
     * @param UserService $userService
     * @param CommandeService $commandeService
     * @return JsonResponse
     * @throws \JsonException
     */
    public function getUserCommande(Request $request, UserService $userService,CommandeService $commandeService):JsonResponse
    {
        $errorDebug = "";
        $jwt = $this->getJwt($request);
        [
            "error"=>$error,
            "errorDebug"=>$errorDebug,
            "commandes"=>$commandes,
        ] = $userService->getCommande($jwt,$commandeService);
        return $this->sendSuccess("recover User Commande",$commandes,response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/me",methods={"GET"},name="")
     * @param Request $request
     * @param UserService $userService
     * @param CommandeService $commandeService
     * @return JsonResponse
     * @throws \JsonException
     */
    public function getUserInfo(Request $request, UserService $userService,CommandeService $commandeService):JsonResponse
    {
        $errorDebug = "";
        $jwt = $this->getJwt($request);
        [
            "error"=>$error,
            "errorDebug"=>$errorDebug,
            "client"=>$client,
        ] = $userService->getUser($jwt);
        return $this->sendSuccess("recover User Commande",$client,response::HTTP_ACCEPTED);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Adresse as AdresseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/adresse", name="app_adresse")
 */
class AdresseController extends CustomAbstractController
{
    /**
     * @Route("/add",methods={"POST"},name="_add")
     * @param Request $request
     * @param AdresseService $adresseService
     * @return JsonResponse
     */
    public function add(Request $request,AdresseService $adresseService): JsonResponse
    {
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        $jwt = $this->getJwt($request);
        $waitedParameters = ["numero"=>"string", "rue"=>"string", "codePostal"=>"string"];
        [
            "error"=>$error,
            "parameters"=>$newParameters,
        ] = $this->checkParameters($parameters,$waitedParameters);
        if ($error !== "") {
            return $this->sendError($error,$error);
        }
        ["error"=>$error,"errorDebug"=>$errorDebug,"adresse"=>$adresse] = $adresseService->add($newParameters,$jwt);
        if ($error !== "") {
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("Adresse created success",$adresse,response::HTTP_CREATED);
    }
}

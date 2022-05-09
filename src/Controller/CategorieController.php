<?php

namespace App\Controller;

use App\Service\Categorie as CategorieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("categorie",name="app_categorie")
 */
class CategorieController extends CustomAbstractController
{
    /**
     * @Route("/add", name="_add")
     */
    public function add(Request $request, CategorieService $categorieService): JsonResponse
    {
        $errorDebug = "";
        $jwt = $this->getJwt($request);
        $parameters = $this->getParameters($request);
        $waitedParameters = [
            "nom"=>"string",
            "image"=>"string",
        ];
        ["error" => $error, "parameters"=>$newParameters] = $this->checkParameters($parameters,$waitedParameters);
        if ($error !== "") {
            return $this->sendError($error,$error);
        }
        [
            "error"=>$error,
            "errorDebug"=>$errorDebug,
            "categorie" => $categorie
        ] = $categorieService->add($newParameters);
        if ($error !== "") {
            return $this->sendError($error,$errorDebug);
        }

        return $this->sendSuccess("Categorie created success",$categorie,response::HTTP_CREATED);
    }
}

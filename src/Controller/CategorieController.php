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

    /**
     * @Route("/all", methods={"GET"} , name="_all")
     * @param CategorieService $categorieService
     * @return JsonResponse
     */
    public function getCategories(CategorieService $categorieService ):JsonResponse
    {
        $errorDebug = "";
        try {
            [
                "error"=>$error,
                "errorDebug"=>$errorDebug,
                "categories"=>$categories,
            ] = $categorieService->getCategories();
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== ""){
            $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("recover categories success",$categories);
    }

    /**
     * @Route("/{id}", methods={"GET"} , name="_one")
     * @param CategorieService $categorieService
     * @param int $id
     * @return JsonResponse
     */
    public function getCategorie(CategorieService $categorieService,int $id):JsonResponse
    {
        $errorDebug = "";
        try {
            [
                "error"=>$error,
                "errorDebug"=>$errorDebug,
                "categorie"=>$categorie,
                "produits"=>$produits,
            ] = $categorieService->getCategorie($id);
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== ""){
            $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("recover categories success",[$categorie,$produits]);
    }
}

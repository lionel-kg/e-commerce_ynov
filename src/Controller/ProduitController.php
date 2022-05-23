<?php

namespace App\Controller;

use App\Controller\CustomAbstractController;
use App\Service\Categorie as CategorieService;
use App\Service\Produit as ProduitService;
use App\Service\Section as SectionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit", name="app_produit")
 */
class ProduitController extends CustomAbstractController
{

    /**
     * @Route("/add", methods={"POST"}, name="add_produit")
     * @param Request $request
     * @param ProduitService $produitService
     * @return JsonResponse
     * @throws \JsonException
     */
    public function add(Request $request,ProduitService $produitService,CategorieService $categorieService, SectionService $sectionService): JsonResponse
    {
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        $jwt = $this->getJwt($request);
        $waitedParameters = [
            "nom"=> "string",
            "prix"=>"float",
            "image"=>"string",
            "couleur"=>"string",
            "description"=>"text",
        ];
        ["error" => $error, "parameters" => $newParameters] = $this->checkParameters($parameters, $waitedParameters);
        if ($error !== "") {
            return $this->sendError($error, $error);
        }
        [
            "error" => $error,
            "errorDebug" => $errorDebug,
            "produit" => $produit
        ] = $produitService->add($parameters,$jwt,$categorieService,$sectionService);
        if ($error !== "") {
            return $this->sendError($error,$errorDebug);
        }

        return $this->sendSuccess("Product created Success",$produit,response::HTTP_CREATED);
    }

    /**
     * @Route("/edit/{id}", methods={"POST"},name="_edit")
     * @param Request $request
     * @param ProduitService $produitService
     * @param int $id
     * @param SectionService $sectionService
     * @param CategorieService $categorieService
     * @return JsonResponse
     * @throws \JsonException
     */
    public function edit(Request $request,ProduitService $produitService,int $id,SectionService $sectionService, CategorieService $categorieService):JsonResponse
    {
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        $jwt = $this->getJwt($request);
        $waitedParameters = [
            "nom_OPT"=>"string",
            "image_OPT"=>"string",
            "couleur_OPT"=>"string",
            "prix_OPT"=>"float",
            "description_OPT"=>"text"
        ];
        ["error"=>$error,"parameters"=>$newParameters] = $this->checkParameters($parameters,$waitedParameters);
        if($error !== ""){
            return $this->sendError($error,$error);
        }
        ["error"=>$error,"errorDebug"=>$errorDebug,"produit"=>$produit] = $produitService->edit($parameters,$jwt,$id,$sectionService,$categorieService);
        if($errorDebug !== ""){
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("Product edit success",$produit);
    }

    /**
     * @Route("/all",methods={"GET"},name="_all")
     * @param ProduitService $produitService
     * @return JsonResponse
     */
    public function getProduits(ProduitService $produitService):JsonResponse
    {
        $errorDebug = "";
        try{
            [
                "error"=>$error,
                "errorDebug"=>$errorDebug,
                "produits"=>$produits,
            ] = $produitService->getProduits();
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if ($errorDebug !== "") {
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("recovery product success",$produits,response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/{id}",methods={"GET"},name="ONE")
     * @param int $id
     * @param ProduitService $produitService
     * @return void
     */
    public function getProduit(int $id,ProduitService $produitService)
    {
        $errorDebug = "";
        try {
            [
                "error"=>$error,
                "errorDebug"=>$errorDebug,
                "produit"=>$produit,
            ] = $produitService->getProduit($id);
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== ""){
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("recover product success",$produit,response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/filtre",methods={"POST"},name="_filter")
     * @param Request $request
     * @param ProduitService $produitService
     * @param CategorieService $categorieService
     * @return JsonResponse
     */
    public function getProductFromCategorie(Request $request,ProduitService $produitService,CategorieService $categorieService, SectionService $sectionService ){
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        try {
            ["error"=>$error,"errorDebug"=>$errorDebug,"produits"=>$produits] = $produitService->getProduitFromCategorie($parameters,$categorieService,$sectionService);
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== ""){
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("Recover product success",$produits,response::HTTP_ACCEPTED);
    }
    /**
     * @Route("/search",methods={"POST"},name="_search")
     * @param Request $request
     * @param ProduitService $produitService
     * @param CategorieService $categorieService
     * @return JsonResponse
     */
    public function searchProductByName(Request $request,ProduitService $produitService,CategorieService $categorieService){
        $errorDebug = "";
        $parameters = $this->getParameters($request);
        try {
            ["error"=>$error,"errorDebug"=>$errorDebug,"produits"=>$produits] = $produitService->searchProduit($parameters,$categorieService);
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== ""){
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("Recover product success",$produits,response::HTTP_ACCEPTED);
    }
}

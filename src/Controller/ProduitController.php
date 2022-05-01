<?php

namespace App\Controller;

use App\Controller\CustomAbstractController;
use App\Service\Produit as ProduitService;
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
     */
    public function add(Request $request,ProduitService $produitService ): JsonResponse
    {
        $parameters = $this->getParameters($request);
        $waitedParameters = [
            "nom"=> "string","stock"=>"int","prix"=>"float","image"=>"string",
        ];
        ["error" => $error, "parameters" => $newParameters] = $this->checkParameters($parameters, $waitedParameters);
        if ($error !== "") {
            return $this->sendError($error, $error);
        }
        [
            "error" => $error,
            "errorDebug" => $errorDebug,
            "produit" => $produit
        ] = $produitService->add($parameters);
        return $this->sendSuccess("Product created Sucess",$produit,response::HTTP_CREATED);
    }
}

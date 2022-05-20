<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Service\Commande as CommandeService;
use App\Service\Produit as ProduitService;
use App\Service\StatutCommande as LigneCommandeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ligne-commande", name="app_ligne_commande")
 */
class LigneCommandeController extends CustomAbstractController
{
    /**
     * @Route("/add", name="_add")
     */
    public function add(Request $request, LigneCommandeService $ligneCommandeService,ProduitService $produitService,CommandeService $commandeService ): JsonResponse
    {
        $errorDebug = "";
        $jwt = $this->getJwt($request);
        $parameters = $this->getParameters($request);
        $produitId = $parameters["id"];
        $waitedParameters = [
            "qte"=>'int',
        ];
        ["error"=>$error,"parameters"=>$newParameters] = $this->checkParameters($parameters,$waitedParameters);
        if ($error !== "") {
            return $this->sendError($error,$error);
        }
        [
            "error" => $error,
            "errorDebug" => $errorDebug,
            "ligneCommande" => $ligneCommande,
        ] = $ligneCommandeService->add($newParameters,$produitService,$produitId,1);
        if ($error !== "") {
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("LigneCommande created success",$ligneCommande,response::HTTP_CREATED);
    }
}

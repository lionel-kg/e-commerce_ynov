<?php

namespace App\Controller;

use App\Service\Commande as CommandeService;
use App\Service\StatutCommande as StatutCommandeService;
use App\Service\LigneCommande as LigneCommandeService;
use App\Service\Produit as ProduitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/commande" ,name="app_commande")
 */
class CommandeController extends CustomAbstractController
{
    /**
     * @Route("/add",methods={"POST"}, name="_add")
     */
    public function add(Request $request,CommandeService $commandeService,LigneCommandeService $ligneCommandeService,ProduitService $produitService,StatutCommandeService $statutCommandeService ): JsonResponse
    {
        $errorDebug = "";
        $jwt = $this->getJwt($request);
        $parameters = $this->getParameters($request);
        $waitedParameter = [
            "prix" => "float",
            "panier"=>"string"
        ];
        ["error"=>$error,"parameters"=>$newParameters] = $this->checkParameters($parameters,$waitedParameter);
        if ($error !== "") {
            return $this->sendError($error,$error);
        }
        [
            "error" => $error,
            "errorDebug" => $errorDebug,
            "commande" => $commande
         ] = $commandeService->add($newParameters,$ligneCommandeService,$produitService,$commandeService,$statutCommandeService,$jwt);
        if ($errorDebug !== "") {
            return $this->sendError($error,$errorDebug);
        }
        return $this->sendSuccess("Commande created success",$commande, response::HTTP_CREATED);
    }
}

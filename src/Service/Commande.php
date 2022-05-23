<?php

namespace App\Service;

use App\Service\Tool\Commande as CommandeTool;
use App\Service\StatutCommande as StatutCommandeService;
use App\Service\LigneCommande as LigneCommandeService;
use App\Service\Taille as TailleService;
use App\Service\Produit as ProduitService;
use App\Service\Commande as CommandeService;
use App\Entity\Commande as CommandeEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class Commande extends CommandeTool
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, SerializerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    public function add(
        array $parameters,
        LigneCommandeService $ligneCommandeService,
        ProduitService $produitService ,
        CommandeService $commandeService,
        StatutCommandeService $statutCommandeService,
        TailleService $tailleService,
        string $jwt )
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","commande"=>[]];
        $user = $this->checktJwt($jwt);
        try {
            $commande = $this->createEntity($parameters);
            $panier = $parameters["panier"];
            $statut = $statutCommandeService->getStatutById(rand(1,6));
            if($statut === null){
                $response["error"] = "Aucun statut trouvé";
            }

            /*for($i = 0; $i < count($array); $i++){
                if($i%2 === 0){
                    $panier[$i]["produit"] = $produitService->findById($array[$i]);
                } else {
                    $panier[$i-1]["qte"] = $array[$i];
                }
            }
            if(count($panier) <= 0 ){
                $response["error"] = "votre panier est vide veuillez le remplir";
            }*/
            foreach ($panier as $ligne ){
                $produit = $produitService->findById($ligne["produit"]);
                if($produit === null ){
                    $response["error"] = "Aucun produit trouvé";
                }
                $res = $ligneCommandeService->add($parameters,$produit,$ligne["qte"]);
                $taille = $tailleService->findTailleById($ligne["size"]);
                $ligneCommande = $res["ligneCommande"];
                $ligneCommande->setTaille($taille);
                $commande->addLigneCommande($ligneCommande);
                $commande->setClient($user);
                $this->em->persist($ligneCommande);
            }
            $commande->setStatutCommande($statut);
            $this->em->persist($commande);
            $this->em->flush();
            $response["commande"] = $commande->getId();
        } catch (\Exception $e){
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== ""){
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de l'ajout de la commande";
        }
        return $response;
    }
    /**
     * @param string $jwt
     * @param CommandeService $commandeService
     * @param int $id
     * @return array
     * @throws \JsonException
     */
    public function getCommandeById(string $jwt,CommandeService $commandeService,int $id):array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","commande"=>[]];
        $client = $this->checktJwt($jwt);
        if($client === null){
            $response["error"] = "Aucun client trouvé";
            return $response;
        }
        try {
            $commande = $commandeService->getCommande($id);
            if ($commande === null) {
                $response["error"] = "Aucune commande trouvé";
                return $response;
            }
            if($commande->getClient() !== $client){
                $response["error"] = "Cette commande ne vous appartient pas";
                return $response;
            }
            $commande = $this->getInfoSerialize([$commande],["info_facture"]);
            $response["commande"] = $commande;
        } catch (\Exception $e) {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la modification de l'utilisateur";
        }
        return $response;
    }
    /**
     * @param $id
     * @return CommandeEntity|null
     */
    public function getCommande($id):?CommandeEntity
    {
        return $this->findById($id);
    }

    /**
     * @param array $filter
     * @return array
     */
    public function getFromFilter(array $filter):array
    {
        return $this->findBy($filter);
    }
}
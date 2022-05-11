<?php

namespace App\Service;

use App\Service\Tool\Commande as CommandeTool;
use App\Service\LigneCommande as LigneCommandeService;
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
        string $jwt )
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","commande"=>[]];
        $user = $this->checktJwt($jwt);
        try {
            $commande = $this->createEntity($parameters);
            $panier = $parameters["panier"];
            /*
                $panier = [];
                $produit1 = $produitService->findById(1);
                $produit2 = $produitService->findById(2);
                $panier[0]["produit"] = $produit1;
                $panier[0]["qte"] = 15;
                $panier[1]["produit"] = $produit2;
                $panier[1]["qte"] = 25;
            */
            foreach ($panier as $produit ){
                $res = $ligneCommandeService->add($parameters,$produit["produit"],$produit["qte"]);
                $ligneCommande = $res["ligneCommande"];
                $commande->addLigneCommande($ligneCommande);
                $this->em->persist($ligneCommande);
            }
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
     * @param $id
     * @return CommandeEntity|null
     */
    public function findById($id):?CommandeEntity
    {
        return $this->em->getRepository(CommnadeEntity::class)->find($id);
    }

    public function findFromFilter(array $filter):array
    {
        return $this->em->getRepository(CommandeEntity::class)->findBy($filter);
    }
}
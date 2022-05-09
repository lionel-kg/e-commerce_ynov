<?php

namespace App\Service;
use App\Entity\Commande as CommandeEntity;
use App\Entity\Produit as ProduitEntity;
use App\Service\Tool\LigneCommande as LigneCommandeTool;
use App\Entity\LigneCommande as LigneCommandeEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class LigneCommande extends LigneCommandeTool
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, SerializerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    /**
     * @param array $parameters
     * @param ProduitEntity $produit
     * @return array
     */
    public function add(
        array $parameters,
        ProduitEntity $produit,
        string $qte
    ):array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","ligneCommande"=>[]];
        try{
            $ligneCommande = $this->createEntity($parameters);
            if($produit === null){
                $response["error"] = "Aucun produit trouvé";
                return $response;
            }
            $ligneCommande->setProduit($produit);
            $ligneCommande->setQte($qte);
            $response["ligneCommande"] = $ligneCommande;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s", $e->getMessage());
        }
        if($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de l'ajout de le ligne de commande";
        }
        return $response;
    }
}
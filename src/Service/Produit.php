<?php

namespace App\Service;

use App\Entity\Produit as ProduitEntity;
use App\Service\Tool\Produit as ProduitServiceTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class Produit extends ProduitServiceTool
{
    private $em;


    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, EntityManagerInterface $serializer, SluggerInterface $slugger)
    {
        parent::__construct($em, $params, $serializer, $slugger);
        $this->em = $em;
    }

    public function add(array $parameter):array{
        $errorDebug = "";
        $response = ["error"=>"", "errorDebug"=>"","produit"=>[]];
        try{
            $produit = $this->createEntity($parameter);
            $this->em->persist($produit);
            $this->em->flush();
            $response["produit"] = $produit->getId();
        } catch (\Exception $e){
            $errorDebug = sprintf('Exception : %s', $e->getMessage());
        }
        if ($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de l'ajout du produit";
        }
        return $response;

    }


}
<?php

namespace App\Service;
use App\Service\Tool\Categorie as CategorieTool;
use App\Entity\Categorie as CategorieEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Categorie extends CategorieTool
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, EntityManagerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    public function add(array $parameters)
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","categorie"=>[]];
        try{
            $categorie = $this->createEntity($parameters);
            $this->em->persist($categorie);
            $this->em->flush();
            $response["categorie"] = $categorie->getId();
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s", $e->getMessage());
        }
        if($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de l'ajout de la categorie";
        }
        return $response;
    }
}
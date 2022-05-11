<?php

namespace App\Service;
use App\Service\Tool\Categorie as CategorieTool;
use App\Entity\Categorie as CategorieEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class Categorie extends CategorieTool
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, SerializerInterface $serializer, SluggerInterface $slugger)
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

    /**
     * @param int $id
     * @return array
     */
    public function getCategorie(int $id) {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","categorie"=>[]];
        try{
            $categorie = $this->findById($id);
            if ($categorie === null) {
                $response["error"] = "Aucune catégorie trouvé";
            }
            $categorie = $this->getInfoSerialize([$categorie],["categorie_info"]);
            dd($categorie);
            $response["categorie"] = $categorie;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s", $e->getMessage());
        }
        if ($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "erreur lors de la récuperation de la categorie";
        }
        return $response;
    }

    /**
     * @return array
     */
    public function getCategories():array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","categories"=>[]];
        try{
            $categories = $this->findAll();
            if($categories === null) {
                $response["error"] = "Aucune categorie trouvée";
            }
            $categories = $this->getInfoSerialize($categories,["categorie_info"]);
            $response["categories"] = $categories;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la récuperation des categories";
        }
        return $response;
    }
}
<?php

namespace App\Service;
use App\Service\Tool\Taille as SectionTool;
use App\Entity\Section as SectionEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class Section extends SectionTool
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
     * @return array
     */
    public function add(array $parameters)
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","section"=>[]];
        try{
            $section = $this->createEntity($parameters);
            $this->em->persist($section);
            $this->em->flush();
            $response["section"] = $section->getId();
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
    public function getSection(int $id) {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","section"=>[],"produit"=>[]];
        try{
            $section = $this->findById($id);
            if ($section === null) {
                $response["error"] = "Aucune section trouvé";
            }
            $produits = $section->getProduits();
            if($produits === null){
                $response["error"] = "Aucune produit trouvé";
            }
            $section = $this->getInfoSerialize([$section],["categorie_info"]);
            $produits = $this->getInfoSerialize([$produits],["produit_info"]);
            $response["section"] = $section;
            $response['produits'] = $produits;
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
     * @param int $id
     * @return array
     */
    public function findSectionById(int $id) {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","section"=>[],"produits"=>[]];
        try{
            $section = $this->findById($id);
            if ($section === null) {
                $response["error"] = "Aucune section trouvé";
            }
            $produits = $section->getProduits();
            $response["section"] = $section;
            $response["produits"] = $produits;
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
    public function getSections():array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","categories"=>[]];
        try{
            $sections = $this->findAll();
            if($sections === null) {
                $response["error"] = "Aucune section trouvée";
            }
            $sections = $this->getInfoSerialize($sections,["categorie_info"]);
            $response["sections"] = $sections;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la récuperation des sections";
        }
        return $response;
    }
}
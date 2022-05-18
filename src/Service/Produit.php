<?php

namespace App\Service;

use App\Entity\Produit as ProduitEntity;
use App\Service\Categorie as CategorieService;
use App\Service\Section as SectionService;
use App\Service\Tool\Produit as ProduitServiceTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class Produit extends ProduitServiceTool
{
    private $em;


    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, SerializerInterface $serializer, SluggerInterface $slugger)
    {
        parent::__construct($em, $params, $serializer, $slugger);
        $this->em = $em;
    }

    /**
     * @param array $parameter
     * @param string $jwt
     * @param CategorieService $categorieService
     * @param Section $sectionService
     * @return array
     * @throws \JsonException
     */
    public function add(array $parameter,string $jwt, CategorieService $categorieService, SectionService $sectionService ):array
    {
        $errorDebug = "";
        $response = ["error"=>"", "errorDebug"=>"","produit"=>[]];
        $user = $this->checktJwt($jwt);
        $isAdmin = in_array("ROLE_ADMIN",$user->getRoles(),true);
        try{
            if($isAdmin === true){
                $produit = $this->createEntity($parameter);
                $categorie = $categorieService->getCategorie($parameter["id"]);
                if($categorie === null){
                    $response["error"] = "Aucune catégorie trouvé";
                    return $response;
                }
                $section = $sectionService->getSection($parameter["sectionId"]);
                if($section === null){
                    $response["error"] = "Aucune section trouvé";
                    return $response;
                }
                $produit->setCategorie($categorie);
                $produit->addSection($section);
                $this->em->persist($produit);
                $this->em->flush();
                $response["produit"] = $produit->getId();
            } else {
                $response["error"] = "L'utilisateur n'est pas un admin";
                return $response;
            }
        } catch (\Exception $e){
            $errorDebug = sprintf('Exception : %s', $e->getMessage());
        }
        if ($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de l'ajout du produit";
        }
        return $response;
    }

    /**
     * @throws \JsonException
     */
    public function edit(array $parameters, string $jwt,string $id):array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","produit"=>[]];
        $user = $this->checktJwt($jwt);
        $isAdmin = in_array("ROLE_ADMIN",$user->getRoles());

        try{
            $produit = $this->findById($id);
            if($isAdmin === true){
                $produit = $this->editEntity($parameters,$produit);
                $this->em->persist($produit);
                $this->em->flush();
                $response["produit"] = $produit;
            } else {
                $response["error"] = "L'utilisateur n'est pas un admin";
                return $response;
            }
        } catch (\Exception $e){
            $response["errorDebug"] = sprintf("Exception : %s",$e->getMessage());
            $response["error"] = "Erreur lors de l'edition du produit";
        }
        return $response;
    }

    /**
     * @return array
     */
    public function getProduits():array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","produits"=>[]];

        try{
            $produits = $this->findAll();
            if($produits === null){
                $response["error"] = "Aucun produit trouvé";
            }
            $produits = $this->getInfoSerialize($produits,["produit_info"]);
            $response["produits"] = $produits;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s" , $e->getMessage());
        }
        if($errorDebug !== ""){
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la récuperation des produits";
        }
        return $response;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getProduit(int $id):array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","produit"=>[]];

        try{
            $produit = $this->findById($id);

            if($produit === null){
                $response["error"] = "Aucun produit trouvé";
            }
            $produit = $this->getInfoSerialize([$produit],["produit_info"]);
            $response["produit"] = $produit;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s" , $e->getMessage());
        }
        if($errorDebug !== ""){
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la récuperation des produits";
        }
        return $response;
    }

    /**
     * @param array $parameters
     * @param Categorie $categorieService
     * @return array
     */
    public function getProduitFromCategorie(array $parameters,CategorieService $categorieService): array
    {
        $errorDebug ="";
        $response = ["error"=>"","errorDebug"=>"","produits"=>[]];
        try {
            $categorie = $categorieService->findById($parameters["categorie"]);
            if ($categorie === null ) {
                $response["error"] = "Aucune categorie trouvé";
            }
            if($parameters["ordre"] === null || $parameters["ordre"] ==="" || !isset($parameters["ordre"])){
                $parameters["ordre"] = "ASC";
            }
            $produits = $this->findFromFilter(["categorie"=>$categorie],["prix"=> $parameters["ordre"]]);
            if($produits === null){
                $response["error"] = "Aucun produit trouvé";
            }
            $produits = $this->getInfoSerialize([$produits],["produit_info"]);
            $response["produits"] = $produits;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s" , $e->getMessage());
        }
        if($errorDebug !== ""){
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la récuperation des produits";
        }
        return $response;
    }

    /**
     * @param array $parameters
     * @param Categorie $categorieService
     * @return array
     */
    public function searchProduit(array $parameters,CategorieService $categorieService): array
    {
        $errorDebug ="";
        $response = ["error"=>"","errorDebug"=>"","produits"=>[]];
        try {
            if(isset($parameters["name"]) || $parameters["name"] !== ""){
                $produits = $this->findByName($parameters["name"]);
            } else {
                $produits = $this->getProduits();
            }
            if($produits === null){
                $response["error"] = "Aucun produit trouvé";
            }
            $produits = $this->getInfoSerialize([$produits],["produit_info"]);
            $response["produits"] = $produits;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s" , $e->getMessage());
        }
        if($errorDebug !== ""){
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la récuperation des produits";
        }
        return $response;
    }

}
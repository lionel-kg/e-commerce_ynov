<?php

namespace App\Service\Tool;

use App\Entity\Produit as ProduitEntity;
use App\Entity\Section as SectionEntity;
use App\Service\CustomAbstractService;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Produit extends CustomAbstractService
{
    private $em;
    private $params;


    /**
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $params
     * @param SerializerInterface $serializer
     * @param SluggerInterface $slugger
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, SerializerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    public function createEntity(array $produitParameters){
        $field = [
            "nom","stock","prix","image","couleur","description",
        ];
        return $this->createSimpleEntity(ProduitEntity::class,$field,$produitParameters);
    }

    /**
     * @param $id
     * @return ProduitEntity|null
     */
    public function findById($id):?ProduitEntity
    {
        return $this->em->getRepository(ProduitEntity::class)->find($id);
    }

    /**
     * @param array $filter
     * @param array $orderBy
     * @return array
     */
    public function findFromFilter(array $filter,array $orderBy = ["prix" => 'ASC']):array
    {
        return $this->em->getRepository(ProduitEntity::class)->findBy($filter,$orderBy);
    }

    /**
     * @param SectionEntity $section
     * @return array
     */
    public function findFromSection(SectionEntity $section):array
    {
        return $this->em->getRepository(ProduitEntity::class)->findFromSection($section);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function findByName(string $name){
        return $this->em->getRepository(ProduitEntity::class)->findByName($name);
    }

    public function findAll():array
    {
        return $this->em->getRepository(ProduitEntity::class)->findAll();
    }

    /**
     * @param array $userParameters
     * @param ProduitEntity $produit
     * @return ProduitEntity|null
     */
    public function editEntity(array $userParameters,ProduitEntity $produit):?ProduitEntity
    {
        $field = [
            "image",
            "nom",
            "prix",
            "couleur",
            "description",
        ];
        return $this->editSimpleEntity($produit,$field,$userParameters);
    }


}
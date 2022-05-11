<?php

namespace App\Service\Tool;

use App\Entity\Categorie as CategorieEntity;
use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Categorie extends CustomAbstractService
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

    /**
     * @param array $parameters
     * @return CategorieEntity
     */
    public function createEntity(array $parameters):CategorieEntity
    {
        $field = [
            "nom",
            "image",
        ];
        return $this->createSimpleEntity(CategorieEntity::class,$field,$parameters);
    }

    /**
     * @param int $id
     * @return CategorieEntity|null
     */
    public function findById(int $id):?CategorieEntity
    {
        return $this->em->getRepository(CategorieEntity::class)->find($id);
    }

    public function findAll():array
    {
        return $this->em->getRepository(CategorieEntity::class)->findAll();
    }
}
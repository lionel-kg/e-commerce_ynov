<?php

namespace App\Service\Tool;

use App\Entity\LigneCommande as LigneCommandeEntity;
use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class LigneCommande extends CustomAbstractService
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
     * @return LigneCommandeEntity|null
     */
    public function createEntity(array $parameters):?LigneCommandeEntity
    {
        $field = [
            "qte",
        ];
        return $this->createSimpleEntity(LigneCommandeEntity::class,$field,$parameters);
    }

    /**
     * @param $id
     * @return LigneCommandeEntity|null
     */
    public function findById($id):?LigneCommandeEntity
    {
        return $this->em->getRepository(LigneCommandeEntity::class)->find($id);
    }

    /**
     * @param array $filter
     * @return array
     */
    public function findFromFilter(array $filter):array
    {
        return $this->em->getRepository(LigneCommandeEntity::class)->findBy($filter);
    }

}
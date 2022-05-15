<?php

namespace App\Service\Tool;

use App\Entity\Commande as CommandeEntity;
use App\Entity\User as UserEntity;
use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Commande extends CustomAbstractService
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
     * @return CommandeEntity
     */
    public function createEntity(array $parameters):CommandeEntity{
        $field = [
            "dateEmission",
            "prix",
        ];
        return $this->createSimpleEntity(CommandeEntity::class,$field,$parameters);
    }

    /**
     * @param $id
     * @return CommandeEntity|null
     */
    public function findById($id):?CommandeEntity
    {
        return $this->em->getRepository(CommandeEntity::class)->find($id);
    }

    /**
     * @param array $filter
     * @return array
     */
    public function findBy(array $filter):array
    {
        return $this->em->getRepository(CommandeEntity::class)->findBy($filter);
    }

}

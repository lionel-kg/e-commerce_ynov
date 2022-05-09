<?php

namespace App\Service\Tool;

use App\Entity\Commande as CommandeEntity;
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

    public function createEntity(array $parameters):CommandeEntity{
        $field = [
            "dateEmission",
            "prix",
        ];
        return $this->createSimpleEntity(CommandeEntity::class,$field,$parameters);
    }
}

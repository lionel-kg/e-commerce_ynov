<?php

namespace App\Service\Tool;

use App\Entity\LigneCommande as LigneCommandeEntity;
use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class LigneCommande extends CustomAbstractService
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, EntityManagerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    /**
     * @param array $parameters
     * @return LigneCommandeEntity
     */
    public function createEntity(array $parameters):LigneCommandeEntity
    {
        $field = [
            "qte",
        ];
        return $this->createSimpleEntity(LigneCommandeEntity::class,$field,$parameters);
    }
}
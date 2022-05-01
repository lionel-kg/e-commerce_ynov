<?php

namespace App\Service\Tool;

use App\Entity\Produit as ProduitEntity;
use App\Service\CustomAbstractService;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Produit extends CustomAbstractService
{
    private $em;
    private $params;


    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, EntityManagerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    public function createEntity(array $produitParameters){
        $field = [
            "nom","stock","prix","image",
        ];
        return $this->createSimpleEntity(ProduitEntity::class,$field,$produitParameters);
    }


}
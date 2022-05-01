<?php

namespace App\Service\Tool;

use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\User as UserEntity;

class User extends CustomAbstractService
{
    private $em;
    private $params;


    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, EntityManagerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    public function createEntity(array $userParameters){
        $field = [
            "email"
        ];
        return $this->createSimpleEntity(UserEntity::class,$field,$userParameters);
    }

}
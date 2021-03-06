<?php

namespace App\Service\Tool;

use App\Entity\Client as ClientEntity;
use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\User as UserEntity;

class User extends CustomAbstractService
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
     * @param array $userParameters
     * @param string $entityClassName
     * @return UserEntity
     */
    public function createEntity(array $userParameters,string $entityClassName):?UserEntity
    {
        $field = [
            "email",
            "nom",
            "prenom",
            "pseudo",
        ];
        return $this->createSimpleEntity($entityClassName,$field,$userParameters);
    }

    /**
     * @param array $userParameters
     * @param UserEntity $user
     * @return UserEntity
     */
    public function editEntity(array $userParameters,UserEntity $user):?UserEntity
    {
        $field = [
            "email",
            "nom",
            "prenom",
            "pseudo",
            "dateNaissance",
        ];
        return $this->editSimpleEntity($user,$field,$userParameters);
    }

    /**
     * @param int $id
     * @return UserEntity|null
     */
    public function findById(int $id): ?UserEntity
    {
        return $this->em->getRepository(ClientEntity::class)->find($id);
    }

    /**
     * @return array
     */
    public function findAllUser():array
    {
        return $this->em->getRepository(ClientEntity::class)->findAll();
    }

}
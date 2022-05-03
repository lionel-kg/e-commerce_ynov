<?php

namespace App\Service;
use \App\Service\Tool\User as UserServiceTool;
use \App\Entity\User as UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class User extends UserServiceTool
{
    private $em;
    private $params;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, EntityManagerInterface $serializer, SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->params = $params;
        $this->passwordHasher = $passwordHasher;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    public function add(array $parameter)
    {
       $errorDebug = "";
       $response = ["error" => "", "errorDebug" => "","user"=> []];
       try {
           $user = $this->createEntity($parameter);
           $user->setPassword($this->passwordHasher->hashPassword($user,$parameter['password']));
           $this->em->persist($user);
           $this->em->flush();
           $response["user"] = $user->getId();
       } catch (\Exception $e){
           $errorDebug = sprintf("Exception : %s", $e->getMessage());
       }
       if($errorDebug !== ""){
           $response["errorDebug"] = $errorDebug;
           $response["error"] = "Erreur lors de l'ajout de l'utilisateur";
       }
       return $response;
    }

    public function edit(array $parameter){
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","user"=>[]];
        try{
            $user = $this->editEntity($parameter);
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $e)
        {
            $errorDebug = sprintf("Exception : %s", $e->getMessage());
        }
        if($errorDebug !== ""){
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la modification de l'utilisateur";
        }
        return $response;
    }
}
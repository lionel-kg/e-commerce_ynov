<?php

namespace App\Service;
use App\Service\Tool\Adresse as AdresseServiceTool;
use App\Entity\Adresse as AdresseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Adresse extends AdresseServiceTool
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, SerializerInterface $serializer, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->params = $params;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    /**
     * @param array $parameters
     * @param string $jwt
     * @return array
     */
    public function add(array $parameters,string $jwt)
    {
        $errorDebug ="";
        $response = ["error" => "", "errorDebug"=>"", "adresse"=> []];
        $user = $this->checktJwt($jwt);

        try {
            $adresse = $this->createEntity($parameters);
            $adresse->addUser($user);
            $user->addAdresse($adresse);
            $this->em->persist($adresse);
            $this->em->persist($user);
            $this->em->flush();
            $response["adresse"] = $adresse->getId();
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s", $e->getMessage());
        }
        if($errorDebug !== ""){
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "erreur lors de la creation de l'adresse";
        }
        return $response;
    }
}
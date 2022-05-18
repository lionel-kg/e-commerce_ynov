<?php

namespace App\Service;

use App\Entity\Admin as AdminEntity;
use App\Service\Commande as CommandeService;
use App\Entity\Client as ClientEntity;
use \App\Service\Tool\User as UserServiceTool;
use \App\Entity\User as UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class User extends UserServiceTool
{
    private $em;
    private $params;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, SerializerInterface $serializer, SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->params = $params;
        $this->passwordHasher = $passwordHasher;
        parent::__construct($em, $params, $serializer, $slugger);
    }

    /**
     * @param array $parameter
     * @param string $entityClassName
     * @return array
     */
    public function add(
        array $parameter,
        string $entityClassName
    )
    {
       $errorDebug = "";
       $response = ["error" => "", "errorDebug" => "","user"=> []];

       try {
           $user = $this->createEntity($parameter,$entityClassName);
           $user->setPassword($this->passwordHasher->hashPassword($user,$parameter['password']));
           if($user instanceOf ClientEntity){
               $role[] = "ROLE_CLIENT";
               $user->setRoles($role);
           }
           if($user instanceOf AdminEntity){
               $role[] = "ROLE_ADMIN";
               $user->setRoles($role);
           }
           $dateNaissance = new \DateTime($parameter["dateNaissance"]);
           $user->setDateNaissance($dateNaissance);
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

    /**
     * @param array $parameter
     * @param string $jwt
     * @return array
     */
    public function edit(array $parameter,string $jwt): array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","user"=>[]];
        try{
            $user = $this->checktJwt($jwt);
            $user = $this->editEntity($parameter,$user);
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

    /**
     * @param string $jwt
     * @param CommandeService $commandeService
     * @return array
     * @throws \JsonException
     */
    public function getCommande(string $jwt,CommandeService $commandeService):array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","commandes"=>[],"client"=>[],"statutCommande"=>[]];
        $client = $this->checktJwt($jwt);
        if($client === null){
            $response["error"] = "Aucun client trouvé";
            return $response;
        }
        try {
            $commandes = $commandeService->getFromFilter(["id"=>3]);
            $commandes = $this->getInfoSerialize($commandes,["commande_info"]);
            $client = $this->getInfoSerialize($client,["user_info"]);
            //$statutCommande = $this->getInfoSerialize($statutCommande,["statut_info"]);

            if ($commandes === null) {
                $response["error"] = "Ce client ne posséde pas de commande";
            }
            $response["commandes"] = $commandes;
            $response["client"] = $client;
        } catch (\Exception $e) {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la modification de l'utilisateur";
        }
       return $response;
    }

    /**
     * @param string $jwt
     * @return array
     * @throws \JsonException
     */
    public function getUser(string $jwt):array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","client"=>[]];
        $client = $this->checktJwt($jwt);
        if($client === null){
            $response["error"] = "Aucun client trouvé";
            return $response;
        }
        try {

            $client = $this->getInfoSerialize([$client],["user_info"]);
            $response["client"] = $client;
        } catch (\Exception $e) {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la modification de l'utilisateur";
        }
        return $response;

    }
}
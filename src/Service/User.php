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
     * @throws \JsonException
     */
    public function edit(array $parameter,string $jwt): array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","user"=>[]];
        try{
            $user = $this->checktJwt($jwt);
            $user = $this->editEntity($parameter,$user);
            if(isset($parameter["nom"]) && $parameter["nom"] === ""){
                $parameter["nom"] = $user->getNom();
            }
            if(isset($parameter["prenom"]) && $parameter["prenom"] === ""){
                $parameter["prenom"] = $user->getPrenom();
            }
            if(isset($parameter["pseudo"]) && $parameter["pseudo"] === "") {
                $parameter["pseudo"] = $user->getPseudo();
            }
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
     * @param array $parameter
     * @param string $jwt
     * @param int $id
     * @return array
     * @throws \JsonException
     */
    public function adminEdit(array $parameter,string $jwt,int $id): array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","user"=>[]];
        $admin = $this->checktJwt($jwt);
        $isAdmin = false;
        if(in_array("ROLE_ADMIN",$admin->getRoles(),true)){
            $isAdmin = true;
        }
        try{
            if($isAdmin === true ) {
                $user = $this->getUserById($id);
                if(isset($parameter["role"])){
                    if(!in_array($parameter["role"],$admin->getRoles(),true)){
                        $user->setRoles([$parameter["role"]]);
                    }
                }
                if(isset($parameter["nom"]) && $parameter["nom"] === ""){
                    $parameter["nom"] = $user->getNom();
                }
                if(isset($parameter["prenom"]) && $parameter["prenom"] === ""){
                    $parameter["prenom"] = $user->getPrenom();
                }
                if(isset($parameter["pseudo"]) && $parameter["pseudo"] === ""){
                    $parameter["pseudo"] = $user->getPseudo();
                }
                $user = $this->editEntity($parameter,$user);
                $this->em->persist($user);
                $this->em->flush();
            } else {
                $response["error"] = "L'utilisateur connecter n'est pas un admin";
            }

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
        $response = ["error"=>"","errorDebug"=>"","commandes"=>[],"statutCommande"=>[]];
        $client = $this->checktJwt($jwt);
        if($client === null){
            $response["error"] = "Aucun client trouvé";
            return $response;
        }
        try {
            $listeCommande = [];
            $commandes = $commandeService->getFromFilter(["client"=>$client]);
            foreach ($commandes as $key =>$commande){
                $listeCommande [$key] = $this->getInfoSerialize([$commande],["commande_info"]);
            }
            if ($commandes === null) {
                $response["error"] = "Ce client ne posséde pas de commande";
            }
            $response["commandes"] = $listeCommande;
            //$response["client"] = $client;
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

    /**
     * @param int $id
     * @return UserEntity|null
     */
    public function getUserById(int $id):?UserEntity
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","user"=>[]];
        try {
            $user = $this->findById($id);
            if($user === null ){
                $response["error"] = "Aucun utilisateur trouvé";
            }
            $response["user"] = $user;
        } catch (\Exception $e) {
            $response["errorDebug"] = sprintf("Exception : %s",$e->getMessage());
            $response["error"] = "Erreur lors de la récuperation de l'utilisateur";
        }
        return $response["user"];
    }

    /**
     * @param string $jwt
     * @return array
     * @throws \JsonException
     */
    public function getAllUser(string $jwt):array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","users"=>[]];
        $admin = $this->checktJwt($jwt);
        $isAdmin = false;
        if($admin === null){
            $response["error"] = "Vous n'êtes pas connecté ";
            return $response;
        }
        if(in_array("ROLE_ADMIN",$admin->getRoles(),true)){
            $isAdmin = true;
        }
        try {
            if($isAdmin === true){
                $users = $this->findAllUser();
                if($users === null){
                    $response["error"] = "Aucun utilisateur trouvé";
                }
                $users = $this->getInfoSerialize([$users],["user_info"]);
                $response["users"] = $users;
            }
        } catch (\Exception $e) {
            $response["errorDebug"] = sprintf("Exception : %s",$e->getMessage());
            $response["error"] = "Erreur lors de la récuperation des utilisateur";
        }
         return $response;
    }
}
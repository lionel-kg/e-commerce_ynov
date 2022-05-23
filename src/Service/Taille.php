<?php

namespace App\Service;
use App\Service\Tool\Taille as TailleTools;
use App\Entity\Taille as TailleEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class Taille extends TailleTools
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
     * @param int $id
     * @return array
     */
    public function findTailleById(int $id) {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","taille"=>[]];
        try{
            $taille = $this->findById($id);
            if ($taille === null) {
                $response["error"] = "Aucune section trouvé";
            }
            $response["taille"] = $taille;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s", $e->getMessage());
        }
        if ($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "erreur lors de la récuperation de la categorie";
        }
        return $response["taille"];
    }

    /**
     * @return array
     */
    public function getTailles():array
    {
        $errorDebug = "";
        $response = ["error"=>"","errorDebug"=>"","tailles"=>[]];
        try{
            $tailles = $this->findAll();
            if($tailles === null) {
                $response["error"] = "Aucune tailles trouvée";
            }
            $response["tailles"] = $tailles;
        } catch (\Exception $e) {
            $errorDebug = sprintf("Exception : %s",$e->getMessage());
        }
        if($errorDebug !== "") {
            $response["errorDebug"] = $errorDebug;
            $response["error"] = "Erreur lors de la récuperation des sections";
        }
        return $response["tailles"];
    }
}
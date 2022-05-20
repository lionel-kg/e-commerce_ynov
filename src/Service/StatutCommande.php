<?php

namespace App\Service;

use App\Service\Tool\StatutCommande as StatutCommandeTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class StatutCommande extends StatutCommandeTool
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
     * @return \App\Entity\StatutCommande|null
     */
    public function getStatutById(int $id){
        return $this->findById($id);
    }
}
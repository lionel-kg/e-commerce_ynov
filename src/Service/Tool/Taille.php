<?php

namespace App\Service\Tool;

use App\Entity\Taille as TailleEntity;
use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Taille extends CustomAbstractService
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
     * @param int $id
     * @return TailleEntity|null
     */
    public function findById(int $id):?TailleEntity
    {
        return $this->em->getRepository(TailleEntity::class)->find($id);
    }

    public function findAll():array
    {
        return $this->em->getRepository(TailleEntity::class)->findAll();
    }

}
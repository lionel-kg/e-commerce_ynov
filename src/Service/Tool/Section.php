<?php

namespace App\Service\Tool;

use App\Entity\Section as SectionEntity;
use App\Service\CustomAbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Section extends CustomAbstractService
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
     * @param array $parameters
     * @return SectionEntity
     */
    public function createEntity(array $parameters):SectionEntity
    {
        $field = [
            "libelle",
        ];
        return $this->createSimpleEntity(SectionEntity::class,$field,$parameters);
    }

    /**
     * @param int $id
     * @return SectionEntity|null
     */
    public function findById(int $id):?SectionEntity
    {
        return $this->em->getRepository(SectionEntity::class)->find($id);
    }

    public function findAll():array
    {
        return $this->em->getRepository(SectionEntity::class)->findAll();
    }
}
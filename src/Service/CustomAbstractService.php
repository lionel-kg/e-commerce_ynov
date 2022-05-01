<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


Abstract class CustomAbstractService
{
    private $params;
    private $em;
    private $serializer;
    private $slugger;

    public function __construct(
        EntityManagerInterface  $em,
        ParameterBagInterface $params,
        EntityManagerInterface $serializer,
        SluggerInterface $slugger
    )
    {
        $this->em = $em;
        $this->params = $params;
        $this->serializer = $serializer;
        $this->slugger = $slugger;
    }
    /**
     * @param string $entityClassName
     * @param array $fields
     * @param array $parameters
     * @return object
     */
    public function createSimpleEntity(
        string $entityClassName,
        array $fields,
        array $parameters
    ): object
    {
        $entity = new $entityClassName();
        foreach ($fields as $field) {
            if (isset($parameters[$field])) {
                $fieldValue = $parameters[$field];
                if ($fieldValue !== null) {
                    $setMethodName = "set".ucfirst($field);
                    $entity->$setMethodName($fieldValue);
                }
            }
        }
        return $entity;
    }

    /**
     * @param object $entity
     * @param array $fields
     * @param array $parameters
     * @return object
     */
    public function editSimpleEntity(
        object $entity,
        array $fields,
        array $parameters
    ): object
    {
        foreach ($fields as $field) {
            if (isset($parameters[$field])){
                $fieldValue = $parameters[$field];
                $setMethodName = "set".ucfirst($field);
                $entity->$setMethodName($fieldValue);
            }
        }
        return $entity;
    }
}
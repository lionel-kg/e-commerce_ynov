<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Taille;

class TaillesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void    {
        /*$tailles = [
            "S",
            "M",
            "L",
            "XL",
            "XXL"
        ];

        foreach($tailles as $t){
            $taille = new Taille();
            $taille->setLibelle($t);
            $manager->persist($taille);
        }

        $manager->flush();*/
    }
}
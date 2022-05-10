<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\StatutCommande;

class StatutCommandeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*$statutsCommande = [
            1 => "En attente de paiement",
            2 => "Payée",
            3 => "En préparation",
            4 => "En cours de livraison",
            5 => "Livrée",
            9 => "Paiement refusé",
        ];

        foreach($statutsCommande as $sCode => $sCommande){
            $statutCommande = new StatutCommande();
            $statutCommande->setLibelle($sCommande);
            $manager->persist($statutCommande);
        }

        $manager->flush();*/
    }
}

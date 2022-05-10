<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Section;

class SectionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $sections = [
            "Homme",
            "Femme",
            "Enfant"
        ];
        foreach($sections as $sec){
            $section = new Section();
            $section->setLibelle($sec);
            $section->setImage("https://cdn.manelli.com/12941-thickbox_default/tee-shirt-de-travail-coton-homme-rouge-toptex.jpg");
            $manager->persist($section);
        }
        $manager->flush();

    }
}

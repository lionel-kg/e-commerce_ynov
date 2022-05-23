<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminFixtures extends Fixture
{
    private $faker;
    private $passwordHasher;

    public function __construct( UserPasswordHasherInterface $hasher)    {
        $this->passwordHasher = $hasher;
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $maxCli = 8;
        for($i = 0; $i < $maxCli; $i++){
            $client = new Client();
            $client->setPrenom($this->faker->firstName);
            $client->setNom($this->faker->lastName);
            $client->setPseudo($this->faker->firstName);
            $client->setEmail(mb_strtolower($client->getPrenom() . "." . $client->getNom() . "@gmail.com"));
            $client->setDateNaissance(new \DateTime("21-05-1991"));
            $client->setPassword($this->passwordHasher->hashPassword($client,"password"));
            $client->setRoles(['ROLE_CLIENT']);
            $client->setActive(true);
            $manager->persist($client);
        }

        $admin = new Admin();
        $admin->setId(100);
        $admin->setPrenom($this->faker->firstName);
        $admin->setNom($this->faker->lastName);
        $admin->setPseudo("admin");
        $admin->setEmail("admin@gmail.com");
        $admin->setDateNaissance(new \DateTime("21-05-1991"));
        $admin->setPassword($this->passwordHasher->hashPassword($admin,"password"));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}

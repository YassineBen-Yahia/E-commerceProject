<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UtilisateurFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i=0; $i<30;$i++){
            $utilisateur = new Utilisateur();
            $utilisateur->setEmail($faker->email());
            $utilisateur->setPassword($faker->password());
            $utilisateur->setName($faker->name());
            $utilisateur->setAdress($faker->address());
//            $role = $faker->randomElement(['ROLE_ADMIN', 'ROLE_USER']);
//            $utilisateur->setRoles($role);

            $manager->persist($utilisateur);

        }

        $manager->flush();
    }
}

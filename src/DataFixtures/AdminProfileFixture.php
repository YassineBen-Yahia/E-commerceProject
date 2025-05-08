<?php

namespace App\DataFixtures;

use App\Entity\AdminProfile;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AdminProfileFixture extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admins = [
            ['name' => 'Mohamed Amine Laouini', 'email' => 'laouini@gmail.com'],
            ['name' => 'Louay Chatti', 'email' => 'chatti@gmail.cim'],
            ['name' => 'Yassine Benyahya', 'email' => 'benyahya@gmail.com'],
            ['name' => 'Aziz bel Haj', 'email' => 'aziz@gmail.com'],
        ];

        foreach ($admins as $adminData) {
            $utilisateur = new Utilisateur();
            $utilisateur->setName($adminData['name']);
            $utilisateur->setEmail($adminData['email']);
            $utilisateur->setAdress($faker->address());
            $utilisateur->setRoles(['ROLE_ADMIN']);

            $plainPassword = "Admin123@";
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $plainPassword);
            $utilisateur->setPassword($hashedPassword);

            $adminProfile = new AdminProfile();
            $adminProfile->setUtilisateur($utilisateur);

            $manager->persist($utilisateur);
            $manager->persist($adminProfile);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['admin-group'];
    }
}

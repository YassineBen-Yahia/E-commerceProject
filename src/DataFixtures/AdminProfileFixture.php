<?php

namespace App\DataFixtures;

use App\Entity\AdminProfile;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AdminProfileFixture extends Fixture
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
            ['name' => 'Mohamed Amine Laouini', 'email' => 'medaminelaouini61@gmail.com'],
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

            // Générer un mot de passe sécurisé de 8 caractères
            $password = $faker->regexify('[A-Z][a-z][0-9][!@#$%&]{4}');
            $utilisateur->setPassword($password);

            $adminProfile = new AdminProfile();
            $adminProfile->setUtilisateur($utilisateur);

            $manager->persist($utilisateur);
            $manager->persist($adminProfile);
        }

        $manager->flush();
    }
}

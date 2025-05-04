<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Repository\CategorieRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProduitFixture extends Fixture implements DependentFixtureInterface,FixtureGroupInterface
{

    private CategorieRepository $repository;
    public function __construct(CategorieRepository $repository)
    {
        $this->repository = $repository;
    }
    public function load(ObjectManager $manager): void

    {
        $faker = Factory::create('fr_FR');

        // Supposons qu'il y a déjà des catégories dans la base, ou que vous ajoutez des références dans une autre fixture
        for ($i = 1; $i <= 9; $i++) {
            $produit = new Produit();
            $produit->setName($faker->word());
            $produit->setDescription($faker->sentence());
            $produit->setPrice($faker->numberBetween(1000, 10000));
            $produit->setStock($faker->numberBetween(10, 100));
            $produit->setImage("img/product0".$i.".jpg");

            // Récupération d'une catégorie existante (à adapter selon vos données ou références)
            // Exemple avec une référence fictive

            $categorie = $this->repository->find(rand(26,30));
            if ($categorie) {
                $produit->setCategorie($categorie);
                $manager->persist($produit);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategorieFixture::class, // si vous avez une fixture pour les catégories
        ];
    }
    public static function getGroups(): array{
        return [
            'produit' // <-- correct way
        ];
    }
}

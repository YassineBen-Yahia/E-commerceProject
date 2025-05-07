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


        $categories = $this->repository->findAll();


        if (count($categories) > 0) {

            $categoryIds = array_map(function ($category) {
                return $category->getId();
            }, $categories);


            for ($i = 1; $i <= 9; $i++) {
                $produit = new Produit();
                $produit->setName($faker->word());
                $produit->setDescription($faker->sentence());
                $produit->setPrice($faker->numberBetween(1000, 10000));
                $produit->setStock($faker->numberBetween(10, 100));
                $produit->setImage("img/product0".$i.".png");


                $randomCategoryId = $categoryIds[array_rand($categoryIds)];


                $categorie = $this->repository->find($randomCategoryId);

                if ($categorie) {
                    $produit->setCategorie($categorie);
                    $manager->persist($produit);
                }
            }

            $manager->flush();
        } else {

            throw new \Exception('No categories found, please load categories first.');
        }
    }


    public function getDependencies(): array
    {
        return [
            CategorieFixture::class,
        ];
    }
    public static function getGroups(): array{
        return [
            'produit'
        ];
    }
}

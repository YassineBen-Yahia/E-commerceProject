<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixture extends Fixture
{
    // Noms personnalisés des catégories
    private array $categories = [
        'Laptops',
        'Smartphones',
        'Headphones',
        'Smartwatches',
        'Desktop Computers',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->categories as $index => $nom) {
            $categorie = new Categorie();
            $categorie->setName($nom);
            $manager->persist($categorie);

            // On ajoute une référence pour chaque catégorie
            $this->addReference($nom, $categorie);
        }

        $manager->flush();
    }
}

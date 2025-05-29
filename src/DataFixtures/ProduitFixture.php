<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Repository\CategorieRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProduitFixture extends Fixture implements DependentFixtureInterface
{
    private CategorieRepository $CategorieRepository;
    public function __construct(CategorieRepository $CategorieRepository){
        $this->CategorieRepository = $CategorieRepository;
    }
    public function load(ObjectManager $manager): void
    {
        $productsByCategory = [
            'Smartphones' => [
                'names' => ['Galaxy S Ultra', 'iPhone Pro Max', 'Pixel Pro', 'OnePlus Nord', 'Xiaomi Note', 'Huawei Nova', 'Realme GT', 'Sony Xperia', 'Asus ROG Phone'],
                'description' => "Découvrez une expérience mobile révolutionnaire avec ce smartphone alliant performance, design et autonomie exceptionnelle."
            ],
            'Smartwatches' => [
                'names' => ['Apple Watch Series', 'Galaxy Watch', 'Fitbit Sense', 'Huawei Watch', 'Garmin Venu', 'Withings ScanWatch', 'Amazfit GTR', 'TicWatch Pro', 'Fossil Gen'],
                'description' => "Restez connecté avec style grâce à cette montre intelligente aux fonctionnalités avancées de santé et de sport."
            ],
            'Desktop Computers' => [
                'names' => ['Dell XPS', 'iMac M1', 'HP Pavilion', 'Asus Zen', 'Acer Aspire', 'Lenovo ThinkCentre', 'Microsoft Surface Studio', 'MSI Modern', 'Alienware Aurora'],
                'description' => "Un ordinateur de bureau puissant et élégant, idéal pour le multitâche, la créativité et les performances professionnelles."
            ],
            'Laptops' => [
                'names' => ['MacBook Air', 'HP Spectre', 'Dell Inspiron', 'Asus ROG Zephyrus', 'Lenovo Yoga', 'Acer Swift', 'MSI Prestige', 'Samsung Galaxy Book', 'Huawei MateBook'],
                'description' => "Alliez portabilité et puissance avec cet ordinateur portable conçu pour vous accompagner partout avec efficacité."
            ],
            'Headphones' => [
                'names' => ['Echo Bass', 'WavePods', 'SoundMax Pro', 'ZenBeats', 'AudioCore', 'Pulse 2', 'NovaPods', 'BeatFlex', 'TuneLite'],
                'description' => "Un accessoire essentiel pour améliorer votre expérience technologique au quotidien, alliant confort et performance."
            ]
        ];

        foreach ($productsByCategory as $categorySlug => $data) {
            /** @var Categorie $category */
           $category = $this->CategorieRepository->findOneBy(['name' => $categorySlug]);

            if (!$category) {
                continue;
            }
            $i=1;
            foreach ($data['names'] as $index => $productName) {
                $product = new Produit();
                $product->setName($productName);
                $product->setDescription($data['description']);
                $product->setPrice(mt_rand(100, 2000));
                $product->setStock(mt_rand(80, 300));
                $product->setImage('img/'.$categorySlug.( $i) . '.jpg');
                $product->setCategorie($category);
                $manager->persist($product);
                $i++;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategorieFixture::class, // le nom de ta fixture catégorie
        ];
    }
}

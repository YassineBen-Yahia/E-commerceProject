<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\AjoutProduitForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProduitController extends AbstractController
{
//    #[Route('/CrerProduit', name: 'app_create')]
//    public function index(): Response
//    {
//        $form = $this->createForm(AjoutProduitForm::class);
//        return $this->render('produit/index.html.twig', [
//            'controller_name' => 'ProduitController',
//
//        ]);
//    }

    #[Route('/produit/new', name: 'app_create_produit')]
    public function createProduit(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AjoutProduitForm::class);
        $form->remove('commandes');
        $form->handleRequest($request);
        $produit = new Produit();
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('produit_directory'),
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                } catch (FileException $e) {

                    // Handle exception if something happens during file upload
                }


            }
        $produit->setStock($form->get('stock')->getData());
        $produit->setName($form->get('name')->getData());
        $produit->setDescription($form->get('description')->getData());
        $produit->setPrice($form->get('price')->getData());
        $produit->setCategorie($form->get('categorie')->getData());

        $entityManager->persist($produit);
        $entityManager->flush();

            // Save the produit entity to the database
            // ...

            return $this->redirectToRoute('app_create_produit');
        }


        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'form' => $form->createView(),
        ]);
    }
}

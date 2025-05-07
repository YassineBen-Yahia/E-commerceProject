<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\AjoutProduitForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
final class ProduitController extends AbstractController
{
    #[Route('/admin/produit/new/{id?0}', name: 'app_create_produit')]
    public function createProduit(Produit $produit = null, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager,$id): Response
    {
        $new = false;
        if (!$produit) {
            $produit = new Produit();
            $new = true;
        }


        $form = $this->createForm(AjoutProduitForm::class, $produit);
        if(!$new){
            $form->remove('categorie');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();


            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('produit_directory'),
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si nécessaire
                }
            }
            if ($new) {
                // Ajout du produit dans la catégorie
                $categorie = $produit->getCategorie();
                if ($categorie) {
                    $categorie->addProduit($produit);
                }
                $this->addFlash('success', "Produit d'id: " . $produit->getId() . " a été ajouté avec succès");
            }else{
                $this->addFlash('success', "Produit d'id: " . $produit->getId() . " a été modifié avec succès");
            }


            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit.list');
        }

        return $this->render('admin_view/product-form.html.twig', [
            'controller_name' => 'ProduitController',
            'form' => $form->createView(),
            'id'=> $id
        ]);
    }

    #[Route('/admin/produit/delete/{id<\d+>}', name: 'produit.delete')]
    public function deleteProduit(Produit $produit = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($produit) {
            $manager = $doctrine->getManager();
            $p_id = $produit->getId();
            $manager->remove($produit);
            $manager->flush();
            $this->addFlash('success', "Produit d'id: " . $p_id . " a été supprimé avec succès");
        }else{
            $this->addFlash('danger', "Produit inexistant . ");
        }
        return $this->redirectToRoute('produit.list');
    }

    #[Route('/admin/produit/list', name: 'produit.list')]
    public function listProduit(ManagerRegistry $doctrine): Response
    {
        $produits = $doctrine->getRepository(Produit::class)->findAll();
        $categories = $doctrine->getRepository(Categorie::class)->findAll();
        return $this->render('admin_view/products-list.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
            'categories' => $categories
        ]);
    }

    #[Route('/details/{id}', name: 'produit_details')]
    public function detailsProduit(Produit $produit , ManagerRegistry $doctrine): Response
    {
        if ($produit) {
            return $this->render('produit/details.html.twig', [
                'produit' => $produit,
            ]);
        } else {
            $this->addFlash('danger', "Produit inexistant . ");
            return $this->redirectToRoute('app_index');
        }
    }
}

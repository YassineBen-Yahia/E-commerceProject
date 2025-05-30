<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Form\AjoutProduitForm;
use App\Repository\CommandeRepository;
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

// src/Controller/ProduitController.php
    #[Route('/admin/orders/view/{id}', name: 'orders.view')]
    public function viewOrder(Commande $commande): Response
    {
        $total = 0;

        foreach ($commande->getCartItems() as $item) {
            $total += $item->getProduit()->getPrice() * $item->getQuantité();
        }

        return $this->render('admin_view/order-view.html.twig', [
            'commande' => $commande,
            'total' => $total,
        ]);
    }

    #[Route('/admin/orders/list/{id?0}', name: 'orders.list')]
    public function listCommandes(ManagerRegistry $doctrine,$id): Response{

        $commandes = $doctrine->getRepository(Commande::class)->findAll();
        return $this->render('admin_view/orders-list.html.twig', [
            'controller_name' => 'ProduitController',
            'commandes' => $commandes,
            'user_id' => $id
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

    #[Route('/admin/statistics/categories', name: 'admin_statistics_categories')]
    public function categorySales(CommandeRepository $commandeRepo): Response
    {
        $commandes = $commandeRepo->findAll();

        $categoryCounts = [];

        foreach ($commandes as $commande) {
            foreach ($commande->getCartItems() as $item) {
                $categorieName = $item->getProduit()->getCategorie()->getName();
                $categoryCounts[$categorieName] = ($categoryCounts[$categorieName] ?? 0) + $item->getQuantité();
            }
        }
        arsort($categoryCounts);

        return $this->render('admin_view/statistics/categories.html.twig', [
            'labels' => json_encode(array_keys($categoryCounts)),
            'data' => json_encode(array_values($categoryCounts))
        ]);
    }

    #[Route('/admin/statistics/sales', name: 'admin_statistics_sales')]
    public function salesStatistics(EntityManagerInterface $em): Response
    {
        $commandes = $em->getRepository(Commande::class)->findAll();

        $salesByMonth = [];
        $monthLabels = [];

        foreach ($commandes as $commande) {
            $monthKey = $commande->getCreatedAt()->format('Y-m');
            $monthLabel = $commande->getCreatedAt()->format('F Y');

            $total = 0;
            foreach ($commande->getCartItems() as $item) {
                $total += $item->getProduit()->getPrice() * $item->getQuantité();
            }

            $salesByMonth[$monthKey] = ($salesByMonth[$monthKey] ?? 0) + $total;
            $monthLabels[$monthKey] = $monthLabel;
        }

        ksort($salesByMonth);
        $salesByMonthLabels = [];
        $salesByMonthData = [];

        foreach (array_keys($salesByMonth) as $monthKey) {
            $salesByMonthLabels[] = $monthLabels[$monthKey];
            $salesByMonthData[] = $salesByMonth[$monthKey];
        }

        return $this->render('admin_view/statistics/sales.html.twig', [
            'salesByMonthLabels' => $salesByMonthLabels,
            'salesByMonthData' => $salesByMonthData,
        ]);
    }


    #[Route('/admin/statistics/sales-by-category', name: 'admin_statistics_sales_by_category')]
    public function salesByCategoryStatistics(EntityManagerInterface $em): Response
    {
        $commandes = $em->getRepository(Commande::class)->findAll();

        $salesData = [];
        $categories = [];
        $monthLabels = [];

        foreach ($commandes as $commande) {

            $monthKey = $commande->getCreatedAt()->format('Y-m');

            $monthLabel = $commande->getCreatedAt()->format('F Y');
            $monthLabels[$monthKey] = $monthLabel;

            foreach ($commande->getCartItems() as $item) {
                $produit = $item->getProduit();
                $category = $produit->getCategorie()->getName();
                $amount = $produit->getPrice() * $item->getQuantité();

                $categories[$category] = true;
                $salesData[$monthKey][$category] = ($salesData[$monthKey][$category] ?? 0) + $amount;
            }
        }


        ksort($salesData);

        ksort($categories);

        $months = [];
        foreach (array_keys($salesData) as $monthKey) {
            $months[] = $monthLabels[$monthKey];
        }

        $categoryNames = array_keys($categories);

        $colorPalette = [
            '#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd',
            '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf'
        ];

        $datasets = [];
        foreach ($categoryNames as $index => $category) {
            $data = [];
            foreach (array_keys($salesData) as $monthKey) {
                $data[] = $salesData[$monthKey][$category] ?? 0;
            }
            $datasets[] = [
                'label' => $category,
                'data' => $data,
                'backgroundColor' => $colorPalette[$index % count($colorPalette)],
                'borderColor' => '#111',
                'borderWidth' => 1
            ];

        }

        return $this->render('admin_view/statistics/sales-by-category.html.twig', [
            'months' => $months,
            'datasets' => $datasets
        ]);
    }


    #[Route('/admin/statistics/top-products', name: 'admin_statistics_top_products')]
    public function topProductsStatistics(CommandeRepository $commandeRepo): Response
    {
        $commandes = $commandeRepo->findAll();

        $productSales = [];

        foreach ($commandes as $commande) {
            foreach ($commande->getCartItems() as $item) {
                $productName = $item->getProduit()->getName();
                $productSales[$productName] = ($productSales[$productName] ?? 0) + $item->getQuantité();
            }
        }

        arsort($productSales); // Sort descending by quantity

        return $this->render('admin_view/statistics/top-products.html.twig', [
            'allSales' => json_encode($productSales)
        ]);
    }


}

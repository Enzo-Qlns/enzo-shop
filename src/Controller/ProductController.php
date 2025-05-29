<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/product/{slug}', name: 'app_product')]
    public function productSlug($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        return $this->render('product/slug.html.twig', [
            'product' => $product
        ]);
    }
}

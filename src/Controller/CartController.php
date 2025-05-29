<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart'=> $cart->getCart(),
            'totalWt' => $cart->getTotalWt()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        //On revcupere le produit
        $product= $productRepository->findOneBy(['id' => $id]);

        //appliquer la fonction add
        $cart->add($product);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart, Request $request): Response
    {

        //appliquer la fonction add
        $cart->decrease($id);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove($id, Cart $cart, Request $request): Response
    {

        //appliquer la fonction remove
        $cart->remove($id);

        $this->addFlash(
            'success',
            message: 'Article supprimé du panier'
        );

        return $this->redirect($request->headers->get('referer'));
    }
}

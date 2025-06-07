<?php

namespace App\Classe;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(
        private RequestStack $requestStack,
    ) {}

    public function add($product)
    {
        //creer un panier
        $cart = $this->requestStack->getSession()->get('cart');

        //verifier si le produit est present dans le panier -> +1 qttié
        //sinon => qtité =1
        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' =>  1
            ];
        }

        //creer la sessions
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function decrease($id)
    {

        //creer un panier
        $cart = $this->requestStack->getSession()->get('cart');

        //verifier si le produit > 1 si oui qty - 1
        //sinon => retirer le produit de cart
        if ($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] - 1;
        } else {
            unset($cart[$id]);
        }

        //maj le panier
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function remove($id)
    {

        //creer un panier
        $cart = $this->requestStack->getSession()->get('cart');
        unset($cart[$id]);

        //maj le panier
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function getTotalQuantity()
    {

        $cart = $this->requestStack->getSession()->get('cart');
        $quantity = 0;

        //corrige si la cart est vide
        if (!isset($cart)) {
            return 0;
        }

        //à chaque prodiut dans ton panier tu incremente la valeur de TotalQuantity

        foreach ($cart as $product) {
            $quantity += $product['qty'];
        }
        return $quantity;
    }



    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }

    public function getTotalWt()
    {
        $cart = $this->requestStack->getSession()->get('cart');
        $price = 0;

        // Check if cart is empty
        if (!isset($cart)) {
            return 0;
        }

        //à chaque prodiut dans ton panier tu incremente la valeur de TotalQuantity
        foreach ($cart as $product) {
            $price += $product['object']->getPriceWithTva() * $product['qty'];
        }
        return $price;
    }
}

<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\OrderType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OrderController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack
    ) {}

    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $addresses = $user->getAddresses();

        if (count($addresses) === 0) {
            return $this->redirectToRoute('app_add_address');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary'),
        ]);

        return $this->render('order/index.html.twig', [
            'orderForm' => $form->createView(),
        ]);
    }

    #[Route('/order/summary', name: 'app_order_summary')]
    public function summary(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() !== 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        /** @var User $user */
        $user = $this->getUser();
        $addresses = $user->getAddresses();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressObj = $form->get('delivery')->getData();
            $address = $addressObj->getFirstName() . ' ' . $addressObj->getLastName() . '<br>' . $addressObj->getAddress();

            $order = new Order();
            $order->setCreatedAt(new \DateTime());
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);
            $order->setUser($user);
            $order->setState('En attente');

            foreach ($cart->getCart() as $item) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProduct($item['object']->getName());
                $orderDetail->setProductIllustration($item['object']->getIllustration());
                $orderDetail->setProductPrice($item['object']->getPrice());
                $orderDetail->setProductQuantity($item['qty']);
                $orderDetail->setProductTva($item['object']->getTva());

                $order->addOrderDetail($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_confirmation', ['id' => $order->getId()]);
        }

        return $this->render('order/order-summary.html.twig', [
            'choices' => $form->get('delivery')->getData(),
            'cart' => $cart->getCart(),
            'totalWt' => $cart->getTotalWt(),
            'orderForm' => $form->createView(),
        ]);
    }

    #[Route('/order/confirmation/{id}', name: 'app_order_confirmation')]
    public function confirmation(Order $order, Cart $cart): Response
    {
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Vider le panier
        $this->requestStack->getSession()->remove('cart');

        return $this->render('order/confirmation.html.twig', [
            'order' => $order
        ]);
    }
}

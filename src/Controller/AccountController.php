<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\PasswordUserType;
use App\Form\CreateAddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\Order;

class AccountController extends AbstractController
{
    #[Route('/my-account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/modify-password', name: 'app_modify_password')]
    public function modify_password(
        Request                     $request,
        EntityManagerInterface      $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'password_hasher' => $userPasswordHasher,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès');
        }

        return $this->render('account/modify-password.html.twig', [
            'modifyPwdForm' => $form->createView()
        ]);
    }

    #[Route('/addresses', name: 'app_addresses')]
    public function addresses(
        EntityManagerInterface $entityManagerInterface
    ): Response {
        $addresses = $entityManagerInterface->getRepository(Address::class)->findBy(['user' => $this->getUser()]);
        return $this->render('account/addresses.html.twig', ['addresses' => $addresses]);
    }

    #[Route('/modify-address', name: 'app_modify_address')]
    public function modify_address(
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response {
        $address = $entityManagerInterface->getRepository(Address::class)->find($request->get('id'));
        
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_addresses');
        }

        $user = $this->getUser();
        $form = $this->createForm(CreateAddressType::class, $address, [
            'user' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($address);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Votre adresse a été modifiée avec succès');
            return $this->redirectToRoute('app_addresses');
        }

        return $this->render('account/modify-address.html.twig', [
            'addressForm' => $form->createView()
        ]);
    }

    #[Route('/add-address', name: 'app_add_address')]
    public function add_address(
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response {
        $address = new Address();
        $user = $this->getUser();
        $form = $this->createForm(CreateAddressType::class, $address, [
            'user' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($address);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Votre adresse a été ajoutée avec succès');
            return $this->redirectToRoute('app_addresses');
        }

        return $this->render('account/add-address.html.twig', [
            'addressForm' => $form->createView()
        ]);
    }

    #[Route('/delete-address/{id}', name: 'app_delete_address')]
    public function delete_address(
        $id,
        EntityManagerInterface $entityManagerInterface
    ): Response {
        $address = $entityManagerInterface->getRepository(Address::class)->find($id);
        
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_addresses');
        }

        $entityManagerInterface->remove($address);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Votre adresse a été supprimée avec succès');
        return $this->redirectToRoute('app_addresses');
    }

    #[Route('/account/orders', name: 'app_orders')]
    public function orders(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $orders = $entityManager->getRepository(Order::class)->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'create_order', methods: ['POST', 'GET'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setDateCreate(new DateTimeImmutable());
            $order->setDateUpdate(new DateTimeImmutable());
            $order->setUser($this->getUser());
            $entityManager->persist($order);
            $entityManager->flush();
            return $this->redirectToRoute('update_order', ['id' => $order->getId()]);
        }
        return $this->render('order/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/order/{id}', name: 'update_order', methods: ['POST', 'GET'])]
    public function update(
        OrderRepository $orderRepository,
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $order = $orderRepository->find($id);
        $this->denyAccessUnlessGranted('edit', $order);
        if (null === $order) {
            throw $this->createNotFoundException(
                'No order found for id ' . $id
            );
        }

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setDateUpdate(new DateTimeImmutable());
            $order->setUser($this->getUser());
            $entityManager->persist($order);
            $entityManager->flush();
            return $this->redirectToRoute('update_order', ['id' => $order->getId()]);
        }
        return $this->render('order/index.html.twig', [
            'form' => $form
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'pay')]
    public function index(Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    {

        // je récupère la session cart
        $cart = $request->getSession()->get('cart');

        // je crée une commande
        $order = new Order();
        
        $cartTotal = 0;

        for($i = 0; $i < count($cart["id"]); $i++) {
            $cartTotal += (float) $cart["price"][$i] * $cart["stock"][$i];
        }

        $order->setTotal($cartTotal);
        $order->setStatus('En cours');
        $order->setUser($this->getUser());
        $order->setDate(new \DateTime);

        
        $entityManager->persist($order);
        $entityManager->flush();

        // pour chaque élément dans mon panier je crée un détail de commande
        for($i = 0; $i < count($cart["id"]); $i++) {

            $orderDetails = new OrderDetails;
            $orderDetails->setIdOrder($orderRepository->findOneBy([], ['id' => 'DESC']));
            $orderDetails->setProduct($productRepository->find($cart["id"][$i]));
            $orderDetails->setQuantity($cart["id"][$i]);

            $entityManager->persist($orderDetails);
            $entityManager->flush();
        }


        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}

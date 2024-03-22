<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response {

      

        return $this->render('cart/index.html.twig', [
            'cartItems' => [],
            'cartTotal' => 100,
        ]);
    }



    #[Route('/cart/{idProduct}', name: 'app_cart_add')]
    public function addProduct(Request $request,
                                ProductRepository $productRepository,
                                int $idProduct): Response {

        //créer la session
        $session = $request->getSession();

        // si la session n'existe pas, je la crée
        if(!$session->get('cart')) {
            $session->set('cart', [
                "id" => [],
                "title" => [],
                "description" => [],
                "picture" => [],
                "stock" => [],
                "price" => [],

            ]);
        }

        $cart = $session->get('cart');

        //ajouter le produit au panier
        //récupérer les infos du produit en BDD et l'ajouter à mon panier
        $product = $productRepository->find($idProduct);
        $cart["id"][] = $product->getId();
        $cart["title"][] = $product->getTitle();
        $cart["description"][] = $product->getDescription();
        $cart["picture"][] = $product->getPicture();
        $cart["price"][] = $product->getPrice();
        $cart["stock"][] = 1;

        $session->set('cart', $cart);
    
       
        //calculer le montant total de mon panier
        $cartTotal = 0;

        for($i = 0; $i < count($session->get('cart')["id"]); $i++) {
            $cartTotal += floatval($session->get('cart')["price"][$i]) * $session->get('cart')["stock"][$i];
        }


        // afficher la page panier
        return $this->render('cart/index.html.twig', [
            'cartItems' => $session->get('cart'),
            'cartTotal' => $cartTotal,
        ]);

    }
}

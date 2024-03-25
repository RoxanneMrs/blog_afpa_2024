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

        // Récupérer les éléments du panier depuis la session
        $session = $request->getSession();
        $cartTotal = 0;

        // Calculer le montant total du panier si j'ai bien une session/des items dans mon panier
        if(!is_null($session->get('cart')) && count($session->get('cart')) > 0) {

           for($i = 0; $i < count($session->get('cart')["id"]); $i++) {
               $cartTotal += (float) $session->get('cart')["price"][$i] * $session->get('cart')["stock"][$i];
           }
        }
   
           return $this->render('cart/index.html.twig', [
               'cartItems' => $session->get('cart'),
               'cartTotal' => $cartTotal,
           ]);
    }



    #[Route('/cart/{idProduct}', name: 'app_cart_add', methods: ['POST'])]
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


//     #[Route('/cart/remove/{idProduct}', name: 'app_cart_remove', methods: ['POST'])]
//    public function removeProduct(Request $request, int $idProduct): Response
//    {
//        $cartItems = $request->getSession()->get('cart', []);

//        foreach ($cartItems['id'] as $i => $idProduct) {
   
//             if (isset($cartItems["id"][$i])) {
//                 unset($cartItems["id"][$i]);
//             }
        
//             $this->addFlash(
//                 'success',
//                 'L\'article a bien été supprimé du panier'
//             );
//         }

//        $request->getSession()->set('cart', $cartItems);

//        return $this->redirectToRoute('app_cart');
//    }



   #[Route('/cart/delete', name: 'app_cart_delete', methods: ['GET'])]
   public function deleteCart (Request $request): Response {

    $session = $request->getSession();
    $session->remove('cart', []);

    return $this->redirectToRoute('app_cart');

   }
}

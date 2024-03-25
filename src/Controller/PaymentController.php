<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'pay')]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        OrderRepository $orderRepository,
        Security $security
    ): Response {

        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {

            // je récupère la session cart
            $cart = $request->getSession()->get('cart');
            // je créé une commande
            $order = new Order;
            $cartTotal = 0;

            for ($i = 0; $i < count($cart["id"]); $i++) {
                $cartTotal += (float) $cart["price"][$i] * $cart["stock"][$i];
            }

            $order->setTotal($cartTotal);
            $order->setStatus('En cours');
            $order->setUser($this->getUser());
            $order->setDate(new \DateTime);

            $entityManager->persist($order);
            $entityManager->flush();

            // pour chaque élément de mon panier je créé un détail de commande
            for ($i = 0; $i < count($cart["id"]); $i++) {
                $orderDetails = new OrderDetails;
                $orderDetails->setIdOrder($orderRepository->findOneBy([], ['id' => 'DESC']));
                $orderDetails->setProduct($productRepository->find($cart["id"][$i]));
                $orderDetails->setQuantity($cart["id"][$i]);

                $entityManager->persist($orderDetails);
                $entityManager->flush();

                $this->redirectToRoute("success");
            }
        }

        $session = $request->getSession();
        $session->set('url_retour', $request->getUri());

        // si pas connecté
        return $this->redirectToRoute('app_login');
    }



    #[Route('/success', name: 'success')]
    public function success(MailerInterface $mailer) : Response {

        // le numéro de la dernière facture pour le user
        // le montant total
        // les produits achetés
        // => récupérer la denrière facture insérée en bdd pour le user
        // et tous les orderDetails liés à cette facture

        // 1 on génère le pdf
        $pdfOptions = new Options();
        $pdfOptions->set(['defaultFont' => 'Arial', 'enable_remote' => true]);
        
        // 2 on crée le pdf avec les options
        $domPdf = new Dompdf($pdfOptions);

        // 3 on prépare le twig qui sera transformé en pdf
        $html = $this->renderView('invoice/index.html.twig', [
            'Amount' => 10,
            'invoiceNumber' => 'F1093',
            'date' => new \DateTime(),
            'products' => []
        ]);

        // 4 on transforme le twig en pdf avec les options de format
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');

        // on enregistre le pdf dans une variable
        $domPdf->render();
        $finalInvoice = $domPdf->output();

        if(!file_exists('uploads/facture')) {
            mkdir('uploads/factures');
        }

        $invoiceNumber = 5;
        $pathInvoice = "./uploads/factures/" . $invoiceNumber . "_" . $this->getUser()->getId() . ".pdf";
        file_put_contents($pathInvoice, $finalInvoice);

        $email = (new TemplatedEmail())
            ->from($this->getParameter('app.mailAddress'))
            ->to($this->getUser()->getEmail())
            ->subject("Facture Blog Afpa 2024")
            ->htmlTemplate("invoice/email.html.twig")
            ->attach($finalInvoice, sprintf('facture-' . $invoiceNumber . 'blog-afpa.pdf', date("Y-m-d")));

        $mailer->send($email);

        return $this->render("payment/success.html.twig", [
            'invoiceNumber' => $invoiceNumber,
            'amount' => 100,
        ]);
    }
}

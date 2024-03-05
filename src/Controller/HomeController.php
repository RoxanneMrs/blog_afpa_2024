<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // symfony dit : si tu as /home tu appelles cette fonction, et tu affiches ceci
    // controller_name = homeController (variables de vues)
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'categories' => $categories,
            'fabrice' => 'Hey voici ma variable de vue Fabrice',
        ]);
    }
}

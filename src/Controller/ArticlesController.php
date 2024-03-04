<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticlesController extends AbstractController
{
    // symfony dit : si tu as /home tu appelles cette fonction, et tu affiches ceci
    #[Route('/articles', name: 'app_articles')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // EntityManagerInterface $entityManager
        // injection de dépendance ! => je récupère entityManager pour interragir avec la bdd
        // https://symfony.com/doc/6.4/doctrine.html#fetching-objects-from-the-database

        // je récupere tout mes articles en BDD en passant par entityManager
        // findAll est une méthode générique permettant de faire un selectAll
        $articles = $entityManager->getRepository(Article::class)->findAll();
        // dd($articles);

        // récupérer tous les articles en BDD
        // et les envoie a ma vue
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    // ici je crée une route pour un article solo que je recuperai par son id.
    #[Route('/article/{id}', name: 'app_article_by_id')]
    public function getArticleById(EntityManagerInterface $entityManager, int $id): Response {

        // pour récupérer le paramètre ID en URL, j'ai juste à le déclarer en argument de ma méthode
        $article = $entityManager->getRepository(Article::class)->find($id);

        return $this->render('articles/show_article.html.twig', [
            'controller_name' => 'ArticlesController',
            'article' => $article,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

class HomeController extends AbstractController
{
    // symfony dit : si tu as /home tu appelles cette fonction, et tu affiches ceci
    // controller_name = homeController (variables de vues)
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, 
    CategoryRepository $categoryRepository,
    PaginatorInterface $paginator,
    Request $request ): Response {


    $articles = $paginator->paginate(
        $articleRepository->findAll(), /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        2 /*limit per page*/
    );
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'categories' => $categoryRepository->findAll(),
            'articles' => $articles,
            // 'fabrice' => 'Hey voici ma variable de vue Fabrice',
        ]);
    }

    #[Route('/search', name: 'app_search_articles', methods: ['GET'])]
    public function getArticleBySearch(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    {

        // si j'ai un param GET search
        if($request->query->has("search")) {

            $search = strtolower($request->query->get("search"));
         
            $articles = $paginator->paginate(
            $articleRepository->findArticleBySearch($search), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
            );


            return $this->render('article/index.html.twig', [
                'articles' => $articles,
            ]);
        
        } else {
            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }
    }


    #[Route('/filter/{filter}', name: 'app_home_filter')]
    public function getArticleByFilter(ArticleRepository $articleRepository, 
                                        CategoryRepository $categoryRepository,
                                        Request $request,
                                        string $filter
                                        ): JsonResponse {
        
        $articlesData = [];

        foreach ($articleRepository->findArticleByFilter($filter) as $article) {

            $articleData = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'descritpion' => $article->getDescritpion(),
                'picture' => $article->getPicture(),
                'date' => $article->getDate()->format("Y-m-d"),
                'category_id' => $article->getCategory() ? $article->getCategory()->getId() : null,
                'category_name' => $article->getCategory() ? $article->getCategory()->getTitle() : null,
            ];

            // Ajouter le tableau simplifié de l'article au tableau des articles
            $articlesData[] = $articleData;
        }

        // Utilisez JsonResponse pour retourner le tableau d'articles en JSON
        return new JsonResponse($articlesData);
    }

}

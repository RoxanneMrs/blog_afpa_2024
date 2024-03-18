<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentsController extends AbstractController
{

    #[Route('/comments', name: 'app_comments')]
    public function index(
    Request $request,
    EntityManagerInterface $entityManager,
    ValidatorInterface $validator,  
    ): Response {

        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request); // permet d'intercepter la requête lancée par la soumission du formulaire

        if($form->isSubmitted()) {

                $comment->setDate(new \DateTime);
                $entityManager->persist($comment); // insérer en base
                $entityManager->flush(); // fermer la transaction executée par la BDD
    
                $this->addFlash(
                    'success',
                    'Votre commentaire a bien été envoyé'
                );
        }

        return $this->render('comments/index.html.twig', [
            'commentForm' => $form,
        ]);
    }

}

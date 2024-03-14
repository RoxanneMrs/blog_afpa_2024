<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function modify(Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            if($form->isValid()) {

                $entityManager->persist($this->getUser());
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Votre profil a bien été mis à jour'
                );

                return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
            }
        }

    

        return $this->render('profile/modify-profile.html.twig', [
            'controller_name' => 'ProfileController',
            'profileForm' => $form,
        ]);
    }

    #[Route('/profile/password/edit', name: 'app_profile_password_edit')]
    public function modifyPassword(Request $request, 
                                    EntityManagerInterface $entityManager, 
                                    UserPasswordHasherInterface $passwordEncoder): Response {

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);


        if($form->isSubmitted()) {

            if ($passwordEncoder->isPasswordValid($user, $form['oldPassword']->getData())) {

                if($form->isValid()) {

                    $newEncodedPassword = $passwordEncoder->hashPassword($user, $form->get('password')->getData());
                    $user->setPassword($newEncodedPassword);

                    $entityManager->persist($this->getUser());
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'Votre mot de passe a bien été modifié'
                    );

                    return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
                }
            }
        }

        return $this->render('profile/modify-password.html.twig', [
            'ChangePasswordForm' => $form,
        ]);
    }
}

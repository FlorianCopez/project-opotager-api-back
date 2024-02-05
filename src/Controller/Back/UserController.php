<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/utilisateurs")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_back_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajouter", name="app_back_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $user->getPassword();
            
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            $user->setCreatedAt(new DateTimeImmutable());

            $userRepository->add($user, true);

            $this->addFlash("success", "L'utilisateur a bien été créé.");

            return $this->redirectToRoute('app_back_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="app_back_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $oldPassword = $user->getPassword();
        
        $form = $this->createForm(UserType::class, $user, ["isNewUser" => false]);
        $form->handleRequest($request);

        
        if ($form->isSubmitted()) {
            $passwordValue = $form->get('password')->getData();

            if ($passwordValue === null) {
                $passwordValue = $oldPassword;
            } else {
                $passwordValue = $userPasswordHasher->hashPassword($user, $passwordValue);
            }

            if($form->isValid()){
                $user->setUpdatedAt(new DateTimeImmutable());
                $user->setPassword($passwordValue);
                
                $userRepository->add($user, true);

                $this->addFlash("success", "Les modifications de l'utilisateur ont bien été prises en compte.");

                return $this->redirectToRoute('app_back_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
            }
        };

        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors que la suppression de l\'utilisateur.');
        }

        return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

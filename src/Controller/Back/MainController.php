<?php

namespace App\Controller\Back;

use App\Repository\GardenRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/back", name="app_back_main_home")
     * 
     * display home page
     */
    public function home(GardenRepository $gardenRepository, UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $numberGardens = count($gardenRepository->findAll());
        $numberUsers = count($userRepository->findAll());

        $gardensList = $paginator->paginate(
            $gardenRepository->findGardensInModeration(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('back/main/home.html.twig', [
            "numberGardens" => $numberGardens,
            "numberUsers" => $numberUsers,
            "pagination" => $gardensList,
        ]);
    }
}

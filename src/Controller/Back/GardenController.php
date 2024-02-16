<?php

namespace App\Controller\Back;

use App\Entity\Garden;
use App\Form\GardenType;
use App\Repository\GardenRepository;
use App\Service\NominatimApiService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/jardins")
 */
class GardenController extends AbstractController
{
    /**
     * @Route("/", name="app_back_garden_index", methods={"GET"})
     * 
     * Route to display the list of gardens
     * 
     * @param GardenRepository $gardenRepository
     * @return Response
     */
    public function index(GardenRepository $gardenRepository): Response
    {
        return $this->render('back/garden/index.html.twig', [
            'gardens' => $gardenRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_garden_show", methods={"GET"})
     * 
     * route to display one garden by ID
     * 
     * @param Garden $garden id of the garden
     * @return Response
     */
    public function show(Garden $garden): Response
    {
        return $this->render('back/garden/show.html.twig', [
            'garden' => $garden,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="app_back_garden_edit", methods={"GET", "POST"})
     * 
     * route to diplay update a garden by ID 
     * 
     * @param Request $request
     * @param Garden $garden id of the garden
     * @param GardenRepository $gardenRepository
     * @param NominatimApiService $nominatimApi
     * @return Response
     */
    public function edit(Request $request, Garden $garden, GardenRepository $gardenRepository, NominatimApiService $nominatimApi): Response
    {
        $form = $this->createForm(GardenType::class, $garden);
        $form->handleRequest($request);

        $coordinatesCity = $nominatimApi->getCoordinates($garden->getCity(), $garden->getLocation());

        if (!$coordinatesCity) {
            $this->addFlash("warning", "L'adresse est introuvable.");
            return $this->renderForm('back/garden/edit.html.twig', [
                'garden' => $garden,
                'form'   => $form,
            ]);
        }
        $garden->setLat($coordinatesCity[ 'lat' ]);
        $garden->setLon($coordinatesCity[ 'lon' ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $garden->setUpdatedAt(new DateTimeImmutable());
            
            $gardenRepository->add($garden, true);

            $this->addFlash("success", "Les modifications du jardin ont bien été prises en compte.");

            return $this->redirectToRoute('app_back_garden_show', ['id' => $garden->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/garden/edit.html.twig', [
            'garden' => $garden,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_garden_delete", methods={"POST"})
     * 
     * path for delete garden by ID
     * 
     * @param Request $request
     * @param Garden $garden id of the the garden
     * @param GardenRepository $gardenRepository
     * @return Response
     */
    public function delete(Request $request, Garden $garden, GardenRepository $gardenRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$garden->getId(), $request->request->get('_token'))) {
            $gardenRepository->remove($garden, true);
            $this->addFlash('success', 'Le jardin a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors que la suppression du jardin.');
        }

        return $this->redirectToRoute('app_back_garden_index', [], Response::HTTP_SEE_OTHER);
    }
}

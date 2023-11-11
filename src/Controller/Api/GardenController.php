<?php

namespace App\Controller\Api;

use App\Entity\Garden;
use App\Repository\GardenRepository;
use App\Service\NominatimApiService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/gardens")
 */
class GardenController extends AbstractController
{
    /**
     * @Route("/", name="app_api_garden_getGardens", methods={"GET"})
     * 
     * get all the gardens
     */
    public function getGardens(GardenRepository $gardenRepository): JsonResponse
    {
        $gardens = $gardenRepository->findAll();

        return $this->json($gardens, Response::HTTP_OK, [], ["groups" => "gardensWithRelation"]);
    }

    /**
     * @Route("/search", name="app_api_garden_getGardensBySearch", methods={"GET"})
     * 
     * get gardens using a town's coordinates
     */
    public function getGardensBySearch(NominatimApiService $nominatimApi, Request $request, GardenRepository $gardenRepository): JsonResponse
    {
        $location = $request->query->get('location');
        $dist = $request->query->get('dist');

        if (empty($location) || !is_numeric($dist)) {
            return $this->json(['error' => 'Paramètres de requête invalides. Assurez-vous que "location" est spécifié et "dist" est un nombre.'], Response::HTTP_BAD_REQUEST);
        }

        $coordinatesCityApi = $nominatimApi->getCoordinates($location);

        if ($coordinatesCityApi === false) {
            return $this->json(['error' => "La ville que vous recherchez est introuvable."], Response::HTTP_BAD_REQUEST);
        }

        $cityLat = $coordinatesCityApi['lat'];
        $cityLon = $coordinatesCityApi['lon'];

        $distance = $dist ?? 10;

        $gardens = $gardenRepository->findGardensByCoordinates($cityLat, $cityLon, $distance);

        return $this->json($gardens, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="app_api_garden_getGardenById", methods={"GET"})
     * 
     * get garden by id
     */
    public function getGardenById(Garden $garden): JsonResponse
    {
        return $this->json($garden, Response::HTTP_OK, [], ["groups" => "gardensWithRelation"]);
    }

    /**
     * @Route("/", name="app_api_garden_postGardens", methods={"POST"})
     * 
     * add garden
     */
    public function postGardens(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em, NominatimApiService $nominatimApi): JsonResponse
    {
        $jsonContent = $request->getContent();

        try {
            $garden = $serializer->deserialize($jsonContent, Garden::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(["error" => "JSON_INVALID"], Response::HTTP_BAD_REQUEST);
        }

        $coordinatesCity = $nominatimApi->getCoordinates($garden->getCity(), $garden->getLocation());

        if (!$coordinatesCity) {
            return $this->json(['error' => "L'adresse est introuvable"], Response::HTTP_BAD_REQUEST);
        }

        $garden->setLat($coordinatesCity['lat']);
        $garden->setLon($coordinatesCity['lon']);

        $errors = $validator->validate($garden);

        if (count($errors) > 0) {
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($garden);
        $em->flush();

        return $this->json([$garden], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_garden_getGardenById", ["id" => $garden->getId()])
        ], [
            "groups" => "gardensWithRelation"
        ]);
    }


    /**
     * @Route("/{id}", name="app_api_garden_putGardenById", methods={"PUT"})
     * 
     * update garden by id
     */
    public function putGardenById(Garden $garden, Request $request, SerializerInterface $serializer, NominatimApiService $nominatimApi, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Garden::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $garden]);

        $coordinatesCityApi = $nominatimApi->getCoordinates($garden->getCity(), $garden->getLocation());

        if (!$coordinatesCityApi) {
            return $this->json(['error' => "L'adresse est introuvable"], Response::HTTP_BAD_REQUEST);
        }

        $garden->setLat($coordinatesCityApi['lat']);
        $garden->setLon($coordinatesCityApi['lon']);
        $garden->setUpdatedAt(new DateTimeImmutable());

        $errors = $validator->validate($garden);

        if (count($errors) > 0) {
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return $this->json($garden, Response::HTTP_OK, [], ["groups" => "gardensWithRelation"]);
    }

    /**
     * @Route("/{id}", name="app_api_garden_deleteGardenById", methods={"DELETE"})
     * 
     * delete garden by id
     */
    public function deleteGardenById(GardenRepository $gardenRepository, Garden $garden = null): JsonResponse
    {
        if (!$garden) {
            return $this->json(['error' => 'Le jardin n\'existe pas.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $gardenRepository->remove($garden, true);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur s\'est produite lors de la suppression du jardin.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(["message" => "Le jardin a bien été supprimé"], Response::HTTP_OK);
    }
}

<?php

namespace App\Controller\Api;

use App\Entity\Picture;
use App\Repository\PictureRepository;
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
 * @Route("/api/pictures")
 */
class PictureController extends AbstractController
{
    /**
     * @Route("/", name="app_api_picture_getPictures", methods={"GET"})
     * 
     * get all pictures
     */
    public function getPictures(PictureRepository $pictureRepository): JsonResponse
    {
        $pictures = $pictureRepository->findAll();

        return $this->json($pictures, Response::HTTP_OK, [], ["groups" => "pictures"]);
    }

    /**
     * @Route("/{id}", name="app_api_picture_getPictureById", methods={"GET"})
     * 
     * get picture by id
     */
    public function getPictureById(Picture $picture): JsonResponse
    {
        return $this->json($picture, Response::HTTP_OK, [], ["groups" => "pictures"]);
    }

    /**
     * @Route("/", name="app_api_picture_postPicture", methods={"POST"})
     * 
     * add picture
     */
    public function postPicture(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $jsonContent = $request->getContent();

        try {
            $picture = $serializer->deserialize($jsonContent, Picture::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(["error" => "JSON_INVALID"], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($picture);

        if (count($errors) > 0) {
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($picture);
        $em->flush();

        return $this->json([$picture], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_picture_getPictureById", ["id" => $picture->getId()])
        ], [
            "groups" => "pictures"
        ]);
    }

    /**
     * @Route("/{id}", name="app_api_picture_putPicture", methods={"PUT"})
     * 
     * update picture by id
     */
    public function putPictureById(Picture $picture, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Picture::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $picture]);

        $picture->setUpdatedAt(new DateTimeImmutable());

        $errors = $validator->validate($picture);

        if (count($errors) > 0) {
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return $this->json($picture, Response::HTTP_OK, [], ["groups" => "pictures"]);
    }

    /**
     * @Route("/{id}", name="app_api_picture_deletePictureById", methods={"DELETE"})
     * 
     * delete picture by id
     */
    public function deletePictureById(PictureRepository $pictureRepository, Picture $picture = null): JsonResponse
    {
        if (!$picture) {
            return $this->json(['error' => 'La photo n\'existe pas.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $pictureRepository->remove($picture, true);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur s\'est produite lors de la suppression de la photo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'La photo à bien été supprimé'], Response::HTTP_OK);
    }
}

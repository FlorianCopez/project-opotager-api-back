<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_api_user_getUsers", methods={"GET"})
     * 
     * get all users
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json($users, Response::HTTP_OK, [], ['groups' => 'users']);
    }

    /**
     * @Route("/{id}", name="app_api_user_getUserById", methods={"GET"})
     * 
     * get user by id
     */
    public function getUserById(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'users']);
    }

    /**
     * @Route("/", name="app_api_user_postUser", methods={"POST"})
     * 
     * add user
     */
    public function postUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $jsonContent = $request->getContent();

        try {
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(["error" => "JSON_INVALID"], Response::HTTP_BAD_REQUEST);
        }

        $password = $user->getPassword();
        $user->setPassword($userPasswordHasher->hashPassword($user, $password));

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([$user], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_user_getUserById", ["id" => $user->getId()])
        ], [
            "groups" => "users"
        ]);
    }

    /**
     * @Route("/{id}", name="app_api_user_putUserById", methods={"PUT"})
     * 
     * update user by id
     */
    public function putUserById(Request $request, SerializerInterface $serializer, User $user, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        $user->setUpdatedAt(new DateTimeImmutable());

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return $this->json($user, Response::HTTP_OK, [], ["groups" => "users"]);
    }

    /**
     * @Route("/{id}", name="app_api_user_deleteUserById", methods={"DELETE"})
     * 
     * delete user by id
     */
    public function deleterUserById(User $user, UserRepository $userRepository): JsonResponse
    {
        if (!$user) {
            return $this->json(['error' => 'L\'utilisateur n\'existe pas.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $userRepository->remove($user, true);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur s\'est produite lors de la suppression de l\'utilisateur.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(["message" => "L'utilisateur a bien été supprimé"], Response::HTTP_OK);
    }
}

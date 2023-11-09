<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\AppProvider;
use App\Entity\Favorite;
use App\Entity\Garden;
use App\Entity\Picture;
use App\Entity\User;
use App\Service\NominatimApiService;
use App\Service\UnsplashApiService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Ottaviano\Faker\Gravatar;


class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $nominatimApiService;
    private $unsplashApiService;

    public function __construct(UserPasswordHasherInterface $passwordHasher, NominatimApiService $nominatimApiService, UnsplashApiService $unsplashApiService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->nominatimApiService = $nominatimApiService;
        $this->unsplashApiService = $unsplashApiService;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $faker->addProvider(new Gravatar($faker));
        $faker->addProvider(new AppProvider);

        // add a user admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@admin.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setPhone('0102030405');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setAvatar($faker->gravatarUrl());
        $manager->persist($admin);

        // add users 
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->unique->userName());
            $user->setEmail($faker->unique->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, $faker->word()));
            $user->setPhone($faker->mobileNumber());
            $user->setRoles([]);
            $user->setAvatar($faker->gravatarUrl());

            $users[] = $user;

            $manager->persist($user);
        }

        // add a gardens
        $gardens = [];
        for ($i = 0; $i < 20; $i++) {
            $garden = new Garden();
            $garden->setTitle($faker->text(110));
            $garden->setDescription($faker->text(400));
            $garden->setLocation($faker->streetAddress());
            $garden->setPostalCode($faker->postcode());
            $garden->setCity($faker->cities());

            $coordinatesCity = $this->nominatimApiService->getCoordinates($garden->getCity());

            $garden->setLat($coordinatesCity['lat']);
            $garden->setLon($coordinatesCity['lon']);
            $garden->setWater($faker->boolean());
            $garden->setTool($faker->boolean());
            $garden->setShed($faker->boolean());
            $garden->setState($faker->text(20));
            $garden->setSurface($faker->numberBetween(1, 20));
            $garden->setPhoneAccess($faker->boolean());
            $garden->setUser($users[array_rand($users)]);

            $gardens[] = $garden;

            $manager->persist($garden);
        }

        // add a pictures
        foreach ($gardens as $garden) {
            $random = mt_rand(0, 4);

            for ($i = 0; $i < $random; $i++) {
                $picture = new Picture();
                $picture->setUrl($this->unsplashApiService->fetchPhotoRandom('potager'));
                $picture->setGarden($garden);

                $manager->persist($picture);
            }
        }

        // add a favorites relations
        for ($i = 0; $i < 15; $i++) {
            $favorite = new Favorite();
            $favorite->setUser($users[array_rand($users)]);
            $favorite->setGarden($gardens[array_rand($gardens)]);

            $manager->persist($favorite);
        }

        $manager->flush();
    }
}

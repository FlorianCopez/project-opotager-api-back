<?php

namespace App\DataFixtures;

use App\Entity\Garden;
use App\Entity\User;
use App\Service\NominatimApiService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Ottaviano\Faker\Gravatar;


class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $nominatimApiService;

    public function __construct(UserPasswordHasherInterface $passwordHasher, NominatimApiService $nominatimApiService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->nominatimApiService = $nominatimApiService;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $faker->addProvider(new Gravatar($faker));

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@admin.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setPhone('0102030405');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setAvatar($faker->gravatarUrl());
        $manager->persist($admin);

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->unique->userName());
            $user->setEmail($faker->unique->email());
            $user->setPassword($faker->password());
            $user->setPhone($faker->mobileNumber());
            $user->setRoles([]);
            $user->setAvatar($faker->gravatarUrl());

            $users[] = $user;

            $manager->persist($user);
        }

        $gardens = [];
        for ($i = 0; $i < 20; $i++) {
            $garden = new Garden();
            $garden->setTitle($faker->text(110));
            $garden->setDescription($faker->text(400));
            $garden->setLocation($faker->streetAddress());
            $garden->setPostalCode($faker->postcode());
            $garden->setCity($faker->city());
            $garden->setLat($this->nominatimApiService->getCoordinates($garden->getCity(), $garden->getLocation())['lat']);
            $garden->setLon($this->nominatimApiService->getCoordinates($garden->getCity(), $garden->getLocation())['lon']);
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

        //! Picture


        $manager->flush();
    }
}

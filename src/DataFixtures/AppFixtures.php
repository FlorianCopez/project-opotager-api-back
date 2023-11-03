<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\AppProvider;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $faker->addProvider(new AppProvider());

        //! User
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@admin.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setPhone('0102030405');
        $admin->setCreatedAt(new DateTimeImmutable());
        $admin->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        //! Garden
        //! Picture


        $manager->flush();
    }
}

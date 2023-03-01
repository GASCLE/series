<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use App\Entity\User;
use ContainerEqUAn2C\get_Security_Command_UserPasswordHash_LazyService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private Generator $faker;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //$this->addSeries();
        $this->addUsers(50);
    }

    public function addSeries(ObjectManager $manager, Generator $generator){

        for ($i = 0; $i < 50; $i++){

            $serie = new Serie();

            $serie
                ->setName(implode(" ", $this->faker->words(nb:3)))
                ->setVote($this->faker->numberBetween(0, 10))
                ->setStatus($this->faker->randomElement(['ended', 'returning', 'canceled']))
                ->setPoster("poster.png")
                ->setTmdbId("123")
                ->setPopularity("250")
                ->setFirstAirDate($this->faker->dateTimeBetween("-6month"))
                ->setLastAirDate($this->faker->dateTimeBetween($serie->getFirstAirDate()))
                ->setGenres($this->faker->randomElement(['Western', 'Comedy', 'Drama']))
                ->setBackdrop("backdrop.png");

            $manager->persist($serie);
        }

        $manager->flush();
    }

    private function addUsers(int $number)
    {

        for ($i = 0; $i < $number; $i++){

            $user = new User();

            $user
                ->setRoles(['ROLE_USER'])
                ->setEmail($this->faker->email)
                ->setFirstname($this->faker->firstName)
                ->setLastname($this->faker->lastName);

                //utilisation du service pour encoder le mdp
                $password = $this->passwordHasher->hashPassword($user, plainPassword: '123');
                $user->setPassword($password);

                $this->entityManager->flush();

        }
    }

}

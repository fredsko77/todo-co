<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Traits\ServicesTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    use ServicesTrait;

    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($u = 0; $u < random_int(44, 59); $u++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, 'P@ssTod0');

            $user->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($password)
                ->setRoles($u % 6 ? ['ROLE_USER'] : ['ROLE_ADMIN'])
                ->setCreatedAt($faker->dateTimeBetween('-5months'))
            ;

            $manager->persist($user);

            for ($t = 0; $t < random_int(0, 98); $t++) {
                $task = new Task();

                $task->setContent($faker->paragraph(4, true))
                    ->setTitle($faker->sentence(10))
                    ->setUser($u % 5 ? $user : null)
                    ->setCreatedAt($faker->dateTimeBetween('-4months'))
                    ->setIsDone($u % 7 ? true : false)
                ;

                $manager->persist($task);
            }
        }

        $manager->flush();
    }
}

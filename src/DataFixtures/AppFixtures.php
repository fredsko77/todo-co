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

        $anonymousTask = new Task();

        $anonymousTask->setContent($faker->paragraph(4, true))
            ->setTitle($faker->sentence(10))
            ->setCreatedAt($faker->dateTimeBetween('-4months'))
            ->setIsDone(false)
        ;

        $manager->persist($anonymousTask);

        $user_ru = new User();

        $user_ru->setEmail('user@todo.fr')
            ->setUsername('user123')
            ->setPassword($this->encoder->encodePassword($user_ru, 'Passuser123'))
            ->setRoles(['ROLE_USER'])
            ->setCreatedAt($this->now())
        ;

        $manager->persist($user_ru);

        for ($ru = 0; $ru < random_int(1, 6); $ru++) {
            $task = new Task();

            $task->setContent($faker->paragraph(4, true))
                ->setTitle($faker->sentence(10))
                ->setUser($user_ru)
                ->setCreatedAt($faker->dateTimeBetween('-4months'))
                ->setIsDone($ru % 6 ? true : false)
            ;

            $manager->persist($task);
        }

        $user_ra = new User();

        $user_ra->setEmail('admin@todo.fr')
            ->setUsername('admin123')
            ->setPassword($this->encoder->encodePassword($user_ru, 'Passadmin123'))
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt($this->now())
        ;

        $manager->persist($user_ra);

        for ($ra = 0; $ra < random_int(1, 5); $ra++) {
            $task = new Task();

            $task->setContent($faker->paragraph(4, true))
                ->setTitle($faker->sentence(10))
                ->setUser($user_ru)
                ->setCreatedAt($faker->dateTimeBetween('-4months'))
                ->setIsDone($ra % 6 ? true : false)
            ;

            $manager->persist($task);
        }

        for ($u = 0; $u < random_int(40, 55); $u++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, 'P@ssTod0');

            $user->setUsername($faker->userName)
                ->setEmail('user-0' . $u . '@todo.fr')
                ->setPassword($password)
                ->setRoles(['ROLE_USER'])
                ->setCreatedAt($faker->dateTimeBetween('-5months'))
            ;

            $manager->persist($user);

            for ($t = 0; $t < random_int(8, 45); $t++) {
                $task = new Task();

                $task->setContent($faker->paragraph(4, true))
                    ->setTitle($faker->sentence(10))
                    ->setUser($user)
                    ->setCreatedAt($faker->dateTimeBetween('-4months'))
                    ->setIsDone($u % 7 ? true : false)
                ;

                $manager->persist($task);
            }
        }

        for ($a = 0; $a < random_int(40, 50); $a++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, 'P@ssTod0');

            $user->setUsername($faker->userName)
                ->setEmail('admin-0' . $a . '@todo.fr')
                ->setPassword($password)
                ->setRoles(['ROLE_ADMIN'])
                ->setCreatedAt($faker->dateTimeBetween('-5months'))
            ;

            $manager->persist($user);

            for ($t = 0; $t < random_int(8, 30); $t++) {
                $task = new Task();

                $task->setContent($faker->paragraph(4, true))
                    ->setTitle($faker->sentence(10))
                    ->setUser($user)
                    ->setCreatedAt($faker->dateTimeBetween('-4months'))
                    ->setIsDone($u % 7 ? true : false)
                ;

                $manager->persist($task);
            }
        }

        for ($t = 0; $t < random_int(1, 180); $t++) {
            $task = new Task();

            $task->setContent($faker->paragraph(4, true))
                ->setTitle($faker->sentence(10))
                ->setCreatedAt($faker->dateTimeBetween('-4months'))
                ->setIsDone($t % 7 ? true : false)
            ;

            $manager->persist($task);
        }

        $manager->flush();
    }
}

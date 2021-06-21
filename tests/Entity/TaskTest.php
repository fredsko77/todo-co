<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{

    public function getTask(): Task
    {
        return (new Task)
            ->setContent('contenu de test')
            ->setCreatedAt()
            ->setIsDone()
            ->setTitle('mon titre test')
            ->setUser($this->getUser())
        ;
    }

    public function getUser(): User
    {
        return (new User)
            ->setUsername('test')
            ->setEmail('test-00@mail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('P@ssTod0')
            ->setCreatedAt()
        ;
    }

    public function assertHasErrors(Task $task, int $number)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($task);
        $this->assertCount($number, $error);
    }

    public function testValidTask()
    {
        $this->assertHasErrors(
            $this->getTask(),
            0
        );
    }

    public function testInvalidContentTask()
    {
        $this->assertHasErrors(
            $this->getTask()->setContent(''),
            0
        );
    }

    public function testInvalidTitleTask()
    {
        $this->assertHasErrors(
            $this->getTask()->setTitle(''),
            0
        );
    }

}

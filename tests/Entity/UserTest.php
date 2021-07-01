<?php
namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{

    public function getEntity(): User
    {
        $user = new User;
        return $user
            ->setUsername('test')
            ->setEmail('test-00@mail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('P@ssTod0')
            ->setCreatedAt()
        ;
    }

    public function assertHasErrors(User $user, int $number)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount($number, $error);
    }

    public function testValidUser()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidPassword()
    {
        $this->assertHasErrors($this->getEntity()->setPassword('machin'), 0);
        $this->assertHasErrors($this->getEntity()->setPassword(''), 0);
    }

    public function testInvalidUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 0);
    }

    public function testInvalidEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 0);
        $this->assertHasErrors($this->getEntity()->setEmail('test-00mail.fr'), 0);
    }

    public function testInvalidRole()
    {
        $this->assertHasErrors($this->getEntity()->setRoles(), 0);
    }
}

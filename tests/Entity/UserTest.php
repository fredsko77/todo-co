<?php
namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function getUser(): User
    {
        return (new User)
            ->setUsername('tatayoyo')
            ->setEmail('test-00@mail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('P@ssTod0')
            ->setCreatedAt()
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt()
        ;
    }

    public function testUserRoles()
    {
        self::bootKernel();
        $user = (new User)
            ->setUsername('test')
            ->setEmail('test-00@mail.com')
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER'])
            ->setPassword('P@ssTod0')
            ->setCreatedAt()
            ->getTasks([])
        ;
        $roles = $this->getUser()->getRoles();
        $this->assertIsArray($roles);
    }

    public function assertHasErrors(User $user, int $number)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount($number, $error);
    }

    public function testValidUser()
    {
        $this->assertHasErrors($this->getUser(), 0);
    }

    public function testInvalidPassword()
    {
        $this->assertHasErrors($this->getUser()->setPassword('Machine'), 1);
        $this->assertHasErrors($this->getUser()->setPassword(''), 1);
    }

    public function testInvalidUsername()
    {
        $this->assertHasErrors($this->getUser()->setUsername(''), 1);
    }

    public function testInvalidEmail()
    {
        $this->assertHasErrors($this->getUser()->setEmail(''), 1);
        $this->assertHasErrors($this->getUser()->setEmail('test-00mail.fr'), 1);
    }

    public function testInvalidRole()
    {
        $this->assertHasErrors($this->getUser()->setRoles(), 0);
    }
}

<?php
namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;

trait Service
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

}

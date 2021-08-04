<?php
namespace App\Tests\Controller;

use App\Tests\Utils;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends Utils
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testHomepageWhenConnected()
    {
        // Se connecter en tant qu'utilisateur ROLE_USER
        $this->createUserClient();
        
        $this->client->request('GET', $this->domain . '/');

        // Assert where are on the homepage by asserting greeting
        static::assertResponseIsSuccessful();
        static::assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");

        // Route is 'app_home'
        static::assertRouteSame('app_home');
    }

    public function test404WhenFakeLink()
    {
        // Assert that not existing route return 404
        $this->client->request('GET', $this->domain . '/-1');
        static::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

}

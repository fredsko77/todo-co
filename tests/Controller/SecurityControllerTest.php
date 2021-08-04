<?php
namespace App\Tests\Controller;

use App\Tests\Utils;
use App\Traits\ServicesTrait;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends Utils
{

    use ServicesTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testdisplayLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'fake-email@dress.com',
            '_password' => 'fakePass3',
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfulLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user@todo.fr',
            '_password' => 'P@ssTod0',
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testAlreadyLoggedUser()
    {
        $this->createUserClient();

        // Créer une requête
        $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}

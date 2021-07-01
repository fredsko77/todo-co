<?php
namespace App\Tests\Controller;

use App\Tests\NeedLogin;
use App\Traits\ServicesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{

    use ServicesTrait;
    use NeedLogin;

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
            '_username' => 'faker-email@dress.com',
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
            '_username' => 'laurent.ferreira@ribeiro.com',
            '_password' => 'P@ssTod0',
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testAlreadyLoggedUser()
    {
        $client = static::createClient();

        $this->login($client);

        // Créer une requête
        $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

    }

}

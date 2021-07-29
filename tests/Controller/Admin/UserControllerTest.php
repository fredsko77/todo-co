<?php
namespace App\Tests\Controller\Admin;

use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    use NeedLogin;

    public function testList()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testDeniedList()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGrantedList()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $client->request('GET', '/admin/users');

        $this->assertSelectorExists('h1', 'Liste des utilisateur');
        $this->assertSelectorExists('.btn.btn-info', 'Créer un utilisateur');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreate()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/users/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testDeniedCreate()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/admin/users/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGrantedCreate()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $client->request('GET', '/admin/users/create');

        $this->assertSelectorExists('h1', 'Créer un utilisateur');
        $this->assertSelectorExists('.btn.btn-success', 'Ajouter');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSubmitCreate()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $crawler = $client->request('GET', '/admin/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'faker7',
            'user[email]' => 'fagathe77@gmail.com',
            'user[roles]' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testValidationSubmitCreate()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $crawler = $client->request('GET', '/admin/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'faker3',
            'user[email]' => 'vincent34@gmail.com',
            'user[roles]' => ['ROLE_USER'],
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEdit()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/users/112/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testDeniedEdit()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/admin/users/112/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGrantedEdit()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $client->request('GET', '/admin/users/112/edit');

        $this->assertSelectorExists('h1', 'Créer un utilisateur');
        $this->assertSelectorExists('.btn.btn-success', 'Modifier');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSubmitEdit()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $crawler = $client->request('GET', '/admin/users/112/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'faker7',
            'user[email]' => 'fagathe77@gmail.com',
            'user[roles]' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testValidationSubmitEdit()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $crawler = $client->request('GET', '/admin/users/112/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'faker3',
            'user[email]' => 'vincent34@gmail.com',
            'user[roles]' => ['ROLE_USER'],
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}

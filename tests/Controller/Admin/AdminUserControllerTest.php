<?php
namespace App\Tests\Controller\Admin;

use App\Tests\Utils;
use Symfony\Component\HttpFoundation\Response;

class AdminUserControllerTest extends Utils
{

    public function testList()
    {
        $this->client->request('GET', $this->domain . '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testDeniedList()
    {
        $this->createUserClient();
        $this->client->request('GET', $this->domain . '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGrantedList()
    {
        $this->createAdminClient();
        $this->client->request('GET', $this->domain . '/admin/users');

        $this->assertSelectorExists('h1', 'Liste des utilisateur');
        $this->assertSelectorExists('.btn.btn-info', 'Créer un utilisateur');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreate()
    {
        $this->client->request('GET', $this->domain . '/admin/users/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testDeniedCreate()
    {
        $this->createUserClient();
        $this->client->request('GET', $this->domain . '/admin/users/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGrantedCreate()
    {
        $this->createAdminClient();
        $this->client->request('GET', $this->domain . '/admin/users/create');

        $this->assertSelectorExists('h1', 'Créer un utilisateur');
        $this->assertSelectorExists('.btn.btn-success', 'Ajouter');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSubmitCreate()
    {
        $this->createAdminClient();
        $crawler = $this->client->request('GET', $this->domain . '/admin/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'faker1',
            'user[email]' => 'fagathe77@gmail.com',
            'user[roles]' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testValidationSubmitCreate()
    {
        $this->createAdminClient();
        $crawler = $this->client->request('GET', $this->domain . '/admin/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'faker' . random_int(93, 393021),
            'user[email]' => 'vincent' . random_int(93, 393021) . '@gmail.com',
            'user[roles]' => ['ROLE_USER'],
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEdit()
    {
        $client = static::createClient();
        $client->request('GET', $this->domain . '/admin/users/' . $this->getUser()->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testDeniedEdit()
    {
        $this->createUserClient();
        $this->client->request('GET', $this->domain . '/admin/users/' . $this->getUser()->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGrantedEdit()
    {
        $this->createAdminClient();
        $this->client->request('GET', $this->domain . '/admin/users/' . $this->getUser()->getId() . '/edit');

        $this->assertSelectorExists('h1', 'Créer un utilisateur');
        $this->assertSelectorExists('.btn.btn-success', 'Modifier');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSubmitEdit()
    {
        $this->createAdminClient();
        $crawler = $this->client->request('GET', $this->domain . '/admin/users/' . $this->getUser()->getId() . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'faker' . random_int(20, 129028),
            'user[email]' => 'fagathe' . random_int(20, 390) . '@gmail.com',
            'user[roles]' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testValidationSubmitEdit()
    {
        $this->createAdminClient();
        $crawler = $this->client->request('GET', $this->domain . '/admin/users/' . $this->getUser()->getId() . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'faker4',
            'user[email]' => 'vincent34@gmail.com',
            'user[roles]' => ['ROLE_USER'],
        ], 'POST');

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

}

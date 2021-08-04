<?php
namespace App\Tests\Controller;

use App\Tests\NeedLogin;
use App\Tests\Utils;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends Utils
{

    use NeedLogin;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRegisterAction()
    {
        $this->client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Inscription');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testRegisterWhenAlreadyLogged()
    {
        $this->createUserClient();
        $this->client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testRegisterActionSubmit()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Enregistrer')->form([
            'registration[username]' => 'faker' . random_int(10, 125000),
            'registration[password][first]' => 'PassTod0',
            'registration[password][second]' => 'PassTod0',
            'registration[email]' => 'fagathe' . random_int(0, 99) . '@gmail.com',
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testInvalidRegisterActionSubmit()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Enregistrer')->form([
            'registration[username]' => 'faker3',
            'registration[password][first]' => 'fakePass',
            'registration[password][second]' => 'fakePass3',
            'registration[email]' => 'fagathe77@gmail.com',
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}

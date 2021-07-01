<?php
namespace App\Tests\Controller;

use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    use NeedLogin;

    public function testRegisterAction()
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Inscription');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testRegisterWhenAlreadyLogged()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testRegister()
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $user = $this->getUser($client);
        $this->login($client, $user);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRegisterActionSubmit()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/register', [
            'registration[username]' => 'faker3',
            'registration[password][first]' => 'fakePass3',
            'registration[password][second]' => 'fakePass3',
            'registration[email]' => 'fagathe77@gmail.com',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testInvalidRegisterActionSubmit()
    {
        $client = static::createClient();
        $client->request('POST', '/register', [
            'registration[username]' => 'faker3',
            'registration[password][first]' => 'fakePass3',
            'registration[password][second]' => 'fakePass3',
            'registration[email]' => 'fagathe77@gmail.com',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}

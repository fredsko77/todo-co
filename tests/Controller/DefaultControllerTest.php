<?php
namespace App\Tests\Controller;

use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{

    use NeedLogin;

    public function testDefaultHomePage()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSelectorTextContains('a.btn.btn-primary', 'Commencer');        
        $this->assertSelectorNotExists('a.btn.btn-success');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function testLoggedHomePage()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/');
        
        $this->assertSelectorTextContains('a.btn.btn-success', 'Créer une nouvelle tâche');
        $this->assertSelectorNotExists('a.btn.btn-primary'); 

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}

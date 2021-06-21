<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testDefaultController()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/users/create');
        $this->assertResponseRedirects('/login');
    }

}

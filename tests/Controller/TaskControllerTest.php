<?php
namespace App\Tests\Controller;

use App\Tests\NeedLogin;
use App\Traits\ServicesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{

    use NeedLogin;
    use ServicesTrait;

    public function testTaskList()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskListWhenAlreadyLogged()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTaskCreate()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskCreateWhenAlreadyLogged()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTaskCreateSubmit()
    {
        $client = static::createClient();
        $client->request('POST', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskCreateSubmitWhenAlreadyLogged()
    {
        $client = static::createClient();
        $this->login($client);
        $crawler = $client->request('POST', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => "Ma super tâche !",
            'task[content]' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero facilis vel vero doloribus assumenda, cupiditate alias, ab accusantium ullam labore et dolores magni cumque nemo fuga natus eius dicta! Velit.',
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testToggleTask()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testToggleTaskByAdmin()
    {
        $client = static::createClient();
        $this->login($client, null, 'admin');
        $task = $this->getAnonymousTask($client);

        $client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testToggleTaskByUser()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $this->login($client, $task->getUser());
        $client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testToggleTaskForbidden()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $this->login($client, null, 'user');
        $client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        // $client->followRedirect();
    }

    public function testTaskEdit()
    {
        $client = static::createClient();
        $task = $this->getTask($client, );
        $client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskEditWhenAlreadyLogged()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $this->login($client, $task->getUser());
        $client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTaskEditSubmit()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $client->request('POST', '/tasks/' . $task->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskEditSubmitWhenAlreadyLogged()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $this->login($client, $task->getUser());
        $crawler = $client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => "Ma super tâche !",
            'task[content]' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero facilis vel vero doloribus assumenda, cupiditate alias, ab accusantium ullam labore et dolores magni cumque nemo fuga natus eius dicta! Velit.',
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskAnonymousEditSubmitWhenAlreadyLogged()
    {
        $client = static::createClient();
        $task = $this->getAnonymousTask($client);
        $this->login($client, null, 'admin');
        $crawler = $client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => "Ma super tâche !",
            'task[content]' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero facilis vel vero doloribus assumenda, cupiditate alias, ab accusantium ullam labore et dolores magni cumque nemo fuga natus eius dicta! Velit.',
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskDelete()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $this->login($client, $task->getUser());
        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }

    public function testTaskDeleteInvalidUser()
    {
        $client = static::createClient();
        $task = $this->getTask($client);
        $this->login($client);
        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testTaskAnonymousDelete()
    {
        $client = static::createClient();
        $task = $this->getAnonymousTask($client);
        $this->login($client, null, 'admin');
        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
    }
}

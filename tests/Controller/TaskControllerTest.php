<?php
namespace App\Tests\Controller;

use App\Entity\Task;
use App\Tests\Utils;
use App\Traits\ServicesTrait;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends Utils
{
    use ServicesTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    private function getAnonymousTask(): Task
    {
        return $this->entityManager->getRepository(Task::class)->findOneBy(['user' => null]);
    }

    private function getTask(): Task
    {
        return $this->entityManager->getRepository(Task::class)->findOneBy(['isDone' => 1]);
    }

    private function getUserTask(): Task
    {
        return $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->getUser()]);
    }

    private function getAdminTask(): Task
    {
        return $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->getAdmin()]);
    }

    public function testTaskList()
    {
        $this->client->request('GET', $this->domain . '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskListWhenAlreadyLogged()
    {
        $this->createUserClient();
        $this->client->request('GET', $this->domain . '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTaskCreate()
    {
        $this->client->request('GET', $this->domain . '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskCreateWhenAlreadyLogged()
    {
        $this->createUserClient();
        $this->client->request('GET', $this->domain . '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTaskCreateSubmit()
    {
        $this->client->request('POST', $this->domain . '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskCreateSubmitWhenAlreadyLogged()
    {
        $crawler = $this->createUserClient();

        $crawler = $this->client->request('GET', $this->domain . "/tasks/create");

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = "Ma super tâche !";
        $form['task[content]'] = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero facilis vel vero doloribus assumenda, cupiditate alias, ab accusantium ullam labore et dolores magni cumque nemo fuga natus eius dicta! Velit.';

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testToggleTask()
    {
        $user = $this->getUser();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $user]);
        $this->client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testToggleTaskByAdmin()
    {
        $crawler = $this->createAdminClient();
        $task = $this->getAnonymousTask();

        $this->client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testToggleTaskByUser()
    {
        $crawler = $this->createUserClient();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->getUser()]);
        $this->client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testToggleTaskForbidden()
    {
        $crawler = $this->createUserClient();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->getAdmin()]);
        $this->client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskEdit()
    {
        $this->client->request('GET', '/tasks/' . $this->getTask()->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskEditWhenAlreadyLogged()
    {
        $crawler = $this->createUserClient();
        $this->client->request('GET', '/tasks/' . $this->getUserTask()->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testTaskEditSubmit()
    {
        $crawler = $this->createUserClient();
        $this->client->request('POST', '/tasks/' . $this->getAdminTask()->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTaskEditSubmitWhenAlreadyLogged()
    {
        $this->createAdminClient();

        $crawler = $this->client->request('GET', '/tasks/' . $this->getAdminTask()->getId() . '/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => "Ma super tâche !",
            'task[content]' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero facilis vel vero doloribus assumenda, cupiditate alias, ab accusantium ullam labore et dolores magni cumque nemo fuga natus eius dicta! Velit.',
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testTaskAnonymousEditSubmitWhenAlreadyLogged()
    {
        $crawler = $this->createAdminClient();
        $crawler = $this->client->request('GET', '/tasks/' . $this->getAnonymousTask()->getId() . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => "Ma super tâche !",
            'task[content]' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero facilis vel vero doloribus assumenda, cupiditate alias, ab accusantium ullam labore et dolores magni cumque nemo fuga natus eius dicta! Velit.',
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskDelete()
    {
        $crawler = $this->client->request('GET', '/tasks/' . $this->getAnonymousTask()->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskDeleteInvalidUser()
    {
        $crawler = $this->createUserClient();
        $this->client->request('GET', '/tasks/' . $this->getAnonymousTask()->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function testTaskAnonymousDelete()
    {
        $this->createAdminClient();
        $this->client->request('GET', '/tasks/' . $this->getAnonymousTask()->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}

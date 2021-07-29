<?php
namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait NeedLogin
{

    public function login(KernelBrowser $client, ?User $user = null, ?string $roles = 'user')
    {
        $user = $user ?? $this->getUser($client, $roles);

        // Crée une session
        $session = $client->getContainer()->get('session');

        // Crée un token
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        // Insère le token dans la session
        $session->set('_security_main', serialize($token));
        $session->save();

        // Insère le cookie de session dans le client
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    final public function getUser(KernelBrowser $client, ?string $roles = 'user')
    {
        return $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([
            'email' => $roles . '@todo.fr',
        ]);
    }

    final public function getTask(KernelBrowser $client)
    {
        return $client->getContainer()->get('doctrine')->getRepository(Task::class)->find(1329);
    }

    final public function getAnonymousTask(KernelBrowser $client)
    {
        return $client->getContainer()->get('doctrine')->getRepository(Task::class)->find(1326);
    }

}

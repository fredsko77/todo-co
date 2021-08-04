<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Utils extends WebTestCase
{
    protected static $application;

    protected $client;

    protected static $container;

    protected $entityManager;

    protected $domain = "http://todo-co";

    protected $crawler;

    /**
     * Create the database | Create tables | Load Fixture
     * Create client | Get container | Get entityManager
     */
    protected function setUp(): void
    {
        // self::runCommand('doctrine:database:create --env=test');
        // self::runCommand('doctrine:migrations:migrate --env=test');
        // self::runCommand('doctrine:fixtures:load -n --env=test');

        $this->client = static::createClient();
        self::$container = $this->client->getContainer();
        $this->entityManager = self::$container->get('doctrine')->getManager();
    }

    /**
     * Run console command line
     *
     * @param string $command
     *
     * @return Int 0 if if everything went fine
     */
    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    /**
     * Get Application
     *
     * @return Application
     */
    protected static function getApplication()
    {
        $client = static::createClient();
        self::$application = new Application($client->getKernel());
        self::$application->setAutoExit(false);

        return self::$application;
    }

    /**
     * Connexion avec un ROLE_ADMIN
     */
    protected function createAdminClient()
    {
        $this->login($this->getAdmin());
    }

    protected function getUser(): User
    {
        for ($i = 0; $i < 50; $i++) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user-0' . $i . '@todo.fr']);
            if ($user instanceof User && $user !== null) {
                return $user;
            }
        }
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user@todo.fr']);
    }

    protected function getAdmin(): User
    {
        for ($i = 0; $i < 50; $i++) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin-0' . $i . '@todo.fr']);
            if ($user instanceof User && $user !== null) {
                return $user;
            }
        }
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@todo.fr']);
    }

    private function login(?User $user = null)
    {
        $user = $user ?? $this->getUser();

        // Crée une session
        $session = $this->client->getContainer()->get('session');

        // Crée un token
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        // Insère le token dans la session
        $session->set('_security_main', serialize($token));
        $session->save();

        // Insère le cookie de session dans le client
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Connexion avec un ROLE_USER
     */
    protected function createUserClient()
    {
        $this->login($this->getAdmin());
    }

    /**
     * Drop database
     */
    protected function tearDown(): void
    {
        // self::runCommand('doctrine:database:drop --force');
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
//

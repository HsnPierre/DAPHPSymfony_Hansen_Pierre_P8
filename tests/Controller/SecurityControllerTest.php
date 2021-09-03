<?php
namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginpageIsUp()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();

        echo $this->client->getResponse()->getContent();
    }

    public function testLoginUser()
    {        
        $userRepository = static::$container->get(UserRepository::class);
        $session = self::$container->get('session');

        $testUser = $userRepository->findOneByUsername('User');

        $firewall = 'main';

        $token = new UsernamePasswordToken($testUser, null, $firewall, $testUser->getRoles());
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        echo $this->client->getResponse()->getContent();       
    }

    public function testLoginAdmin()
    {        
        $userRepository = static::$container->get(UserRepository::class);
        $session = self::$container->get('session');

        $testUser = $userRepository->findOneByUsername('Admin');

        $firewall = 'main';

        $token = new UsernamePasswordToken($testUser, null, $firewall, $testUser->getRoles());
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful(); 

        echo $this->client->getResponse()->getContent();       
    }
}
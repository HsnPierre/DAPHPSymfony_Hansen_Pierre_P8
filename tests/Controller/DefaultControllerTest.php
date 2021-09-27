<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testHomepageRedirection()
    {
        $this->client->request('GET', '/');
        $crawler = $this->client->followRedirect();

        $this->assertSame("Se connecter", $crawler->filter('a.btn.btn-success')->text());

        echo $this->client->getResponse()->getContent();  
    }

    public function testHomepageAccessLogged()
    {
        $this->logIn();
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        echo $this->client->getResponse()->getContent();  
    }

    public function logIn()
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
    }
}
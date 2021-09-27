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
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['login[username]'] = 'User';
        $form['login[password]'] = 'password';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame("Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !", $crawler->filter('h1')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testLoginAdmin()
    {        
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['login[username]'] = 'Admin';
        $form['login[password]'] = 'password';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame("Gérer les utilisateurs", $crawler->filter('a.btn.btn-primary')->text());

        echo $this->client->getResponse()->getContent();      
    }
}
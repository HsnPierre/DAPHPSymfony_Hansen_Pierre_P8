<?php
namespace App\Tests\Controller;

use Faker;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
class UserControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testUsersDashboardLogged()
    {
        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();

        echo $this->client->getResponse()->getContent();
    }
    
    public function testCreateNewUserV()
    {
        $faker = Faker\Factory::create();

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $faker->userName();
        $form['user[plainPassword][first]'] = '&Azertyuiop1';
        $form['user[plainPassword][second]'] = '&Azertyuiop1';
        $form['user[email]'] = $faker->safeEmail();

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewUserBlankForm()
    {
        $faker = Faker\Factory::create();

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = '';
        $form['user[plainPassword][first]'] = '&Azertyuiop1';
        $form['user[plainPassword][second]'] = '&Azertyuiop1';
        $form['user[email]'] = $faker->safeEmail();

        $crawler = $this->client->submit($form);

        $this->assertSame("Vous devez saisir un nom d'utilisateur.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewUserWrongUsername()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('User');

        $faker = Faker\Factory::create();

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $testUser->getUsername();
        $form['user[plainPassword][first]'] = '&Azertyuiop1';
        $form['user[plainPassword][second]'] = '&Azertyuiop1';
        $form['user[email]'] = $faker->safeEmail();

        $crawler = $this->client->submit($form);

        $this->assertSame("Le pseudo est déjà utilisé.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewUserWrongPassword()
    {
        $faker = Faker\Factory::create();

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $faker->userName();
        $form['user[plainPassword][first]'] = 'a';
        $form['user[plainPassword][second]'] = 'a';
        $form['user[email]'] = $faker->safeEmail();

        $crawler = $this->client->submit($form);

        $this->assertSame("Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et faire au moins 12 caractères", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewUserWrongPassword2()
    {
        $faker = Faker\Factory::create();

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $faker->userName();
        $form['user[plainPassword][first]'] = 'a';
        $form['user[plainPassword][second]'] = 'b';
        $form['user[email]'] = $faker->safeEmail();

        $crawler = $this->client->submit($form);

        $this->assertSame("Les deux mots de passe doivent correspondre.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewUserWrongMail()
    {
        $faker = Faker\Factory::create();

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $faker->userName();
        $form['user[plainPassword][first]'] = '&Azertyuiop1';
        $form['user[plainPassword][second]'] = '&Azertyuiop1';
        $form['user[email]'] = 'a';

        $crawler = $this->client->submit($form);

        $this->assertSame("Le format de l'adresse n'est pas correcte.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewUserWrongMail2()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('User');

        $faker = Faker\Factory::create();

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $faker->userName();
        $form['user[plainPassword][first]'] = '&Azertyuiop1';
        $form['user[plainPassword][second]'] = '&Azertyuiop1';
        $form['user[email]'] = $testUser->getEmail();

        $crawler = $this->client->submit($form);

        $this->assertSame("L'adresse mail est déjà utilisée.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewUserDenied()
    {
        $this->logInUser();
        $crawler = $this->client->request('GET', '/users/create');

        $this->assertResponseStatusCodeSame(403, 'Access Denied.');

        echo $this->client->getResponse()->getContent();
    }

    public function testEditUser()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('username')?$userRepository->findOneByUsername('username'):$userRepository->findOneByUsername('username_edit');

        $this->logInAdmin();
        $crawler = $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $user->getUsername() == 'username' ? $form['user[username]'] = 'username_edit' : $form['user[username]'] = 'username';
        $form['user[plainPassword][first]'] = '&Azertyuiop1';
        $form['user[plainPassword][second]'] = '&Azertyuiop1';
        $user->getEmail() == 'adress@email.com' ? $form['user[email]'] = 'adress_edit@email.com' : $form['user[email]'] = 'adress@email.com';

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        echo $this->client->getResponse()->getContent();
    }

    public function testEditUserDenied()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('username')?$userRepository->findOneByUsername('username'):$userRepository->findOneByUsername('username_edit');

        $this->logInUser();
        $crawler = $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $this->assertResponseStatusCodeSame(403, 'Access Denied.');

        echo $this->client->getResponse()->getContent();
    }

    public function logInAdmin()
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
    }

    public function logInUser()
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
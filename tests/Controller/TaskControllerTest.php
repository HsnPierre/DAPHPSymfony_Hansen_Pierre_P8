<?php
namespace App\Tests\Controller;

use Faker;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testShowTaskRedirection()
    {
        $this->client->request('GET', '/tasks');
        $crawler = $this->client->followRedirect();

        $this->assertSame("Se connecter", $crawler->filter('a.btn.btn-success')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testShowTaskLogged()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();

        echo $this->client->getResponse()->getContent();
    }
    
    public function testCreateNewTask()
    {
        $faker = Faker\Factory::create();

        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = $faker->word();
        $form['task[content]'] = $faker->paragraph(3);

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        echo $this->client->getResponse()->getContent();
    }
    
    public function testCreateNewTaskBlankTitle()
    {

        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = '';
        $form['task[content]'] = 'content';

        $crawler = $this->client->submit($form);

        $this->assertSame("Vous devez saisir un titre.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testCreateNewTaskBlankContent()
    {

        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'title';
        $form['task[content]'] = '';

        $crawler = $this->client->submit($form);

        $this->assertSame("Vous devez saisir du contenu.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }  

    public function testEditTask()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('User');

        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $testUser]);

        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = $task->getTitle().'_EDIT';
        $form['task[content]'] = 'EDIT '.$task->getContent();

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        echo $this->client->getResponse()->getContent();
    }

    public function testEditTaskBlankForm()
    {

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('User');

        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $testUser]);

        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = '';
        $form['task[content]'] = 'content';

        $crawler = $this->client->submit($form);

        $this->assertSame("Vous devez saisir un titre.", $crawler->filter('li')->text());

        echo $this->client->getResponse()->getContent();
    }

    public function testEditTaskDenied()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('Admin');

        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $testUser])
        ;
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');

        $this->assertResponseStatusCodeSame(403, 'Access Denied.');

        echo $this->client->getResponse()->getContent();
    }

    public function testToggleTask()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('User');

        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $testUser]);
        $this->logIn();
        
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        echo $this->client->getResponse()->getContent();
    }

    public function testToggleTaskDenied()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('Admin');

        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $testUser])
        ;
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $this->assertResponseStatusCodeSame(403, 'Access Denied.');

        echo $this->client->getResponse()->getContent();
    }

    public function testDeleteTask()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('User');

        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $testUser]);
        $this->logIn();
        
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        echo $this->client->getResponse()->getContent();
    }

    public function testDeleteTaskAdmin()
    {
        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => null]);
        $this->logInAdmin();
        
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        echo $this->client->getResponse()->getContent();
    }

    public function testDeleteTaskDenied()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('Admin');

        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $testUser])
        ;
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertResponseStatusCodeSame(403, 'Access Denied.');

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
}
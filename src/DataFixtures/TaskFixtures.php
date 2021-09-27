<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Task;
use Faker;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        
        for($i = 0; $i < 15; $i++){
            $tmp_author = [null, $this->getReference('user-'.rand(0,4))];
            $task = new Task();

            $task->setTitle($faker->word());
            $task->setContent($faker->paragraph(3));
            $task->setAuthor($tmp_author[array_rand($tmp_author, 1)]);

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}

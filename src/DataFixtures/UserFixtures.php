<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tmp_password = 'password';
        $tmp_roles = [['ROLE_USER'],['ROLE_USER','ROLE_ADMIN']];
        $faker = Faker\Factory::create();

        $user = new User();
        $user_reference = 'user-0';

        $user->setUsername('User');
        $user->setEmail('adress@mail.com');
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword(password_hash($tmp_password, PASSWORD_BCRYPT));

        $this->addReference($user_reference, $user);

        $manager->persist($user);

        $user = new User();
        $user_reference = 'user-1';

        $user->setUsername('Admin');
        $user->setEmail('admin@mail.com');
        $user->setRoles(["ROLE_USER","ROLE_ADMIN"]);
        $user->setPassword(password_hash($tmp_password, PASSWORD_BCRYPT));

        $this->addReference($user_reference, $user);

        $manager->persist($user);

        $user = new User();
        $user_reference = 'user-2';

        $user->setUsername('username');
        $user->setEmail('adress@email.com');
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword(password_hash($tmp_password, PASSWORD_BCRYPT));

        $this->addReference($user_reference, $user);

        $manager->persist($user);

        for($i = 3; $i < 8; $i++){
            $user = new User();
            $user_reference = 'user-'.$i;

            $user->setUsername($faker->userName());
            $user->setEmail($faker->safeEmail());
            $user->setRoles($tmp_roles[array_rand($tmp_roles, 1)]);
            $user->setPassword(password_hash($tmp_password, PASSWORD_BCRYPT));

            $this->addReference($user_reference, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

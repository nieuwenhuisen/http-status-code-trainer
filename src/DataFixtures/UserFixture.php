<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const DEFAULT_PASSWORD = 'Test1234';

    private $faker;
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->faker = Factory::create();
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Create admin
        $admin = $this->createUser('admin@admin.com', self::DEFAULT_PASSWORD, ['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('admin', $admin);

        // Create 10 random users
        for ($i = 1; $i <= 10; $i++) {
            $user = $this->createUser($this->faker->email, self::DEFAULT_PASSWORD);
            $manager->persist($user);
            $this->addReference('user_'.$i, $admin);
        }

        $manager->flush();
    }

    private function createUser(string $email, string $plainPassword, array $roles = []): User
    {
        $user = new User($email, $roles);

        $password = $this->userPasswordEncoder->encodePassword($user, $plainPassword);
        $user->changePassword($password);

        return $user;
    }
}

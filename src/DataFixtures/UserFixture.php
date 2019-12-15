<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const DEFAULT_PASSWORD = 'Test1234';

    /**
     * List with dummy users.
     * email, roles, plain password
     */
    private const USERS = [
        'admin' => ['admin@admin.com', self::DEFAULT_PASSWORD, ['ROLE_ADMIN']],
        'user_1' => ['user1@user.com', self::DEFAULT_PASSWORD, ['ROLE_USER']],
        'user_2' => ['user2@user.com', self::DEFAULT_PASSWORD, ['ROLE_USER']],
        'user_3' => ['user3@user.com', self::DEFAULT_PASSWORD, ['ROLE_USER']],
    ];

    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $index => [$email, $password, $roles]) {
            $user = new User($email, $roles);
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $password));
            $manager->persist($user);
            $this->addReference($index, $user);
        }

        $manager->flush();
    }
}

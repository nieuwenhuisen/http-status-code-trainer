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
     * email, roles, plain password.
     */
    public const USERS = [
        'admin' => ['admin@admin.com', self::DEFAULT_PASSWORD, ['ROLE_ADMIN'], false],
        'user_1' => ['user1@user.com', self::DEFAULT_PASSWORD, ['ROLE_USER'], false],
        'user_2' => ['user2@user.com', self::DEFAULT_PASSWORD, ['ROLE_USER'], false],
        'user_3' => ['user3@user.com', self::DEFAULT_PASSWORD, ['ROLE_USER'], false],
        'user_mfa' => ['user4@user.com', self::DEFAULT_PASSWORD, ['ROLE_USER'], '2ADFDSJF86'],
    ];

    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $index => [$email, $password, $roles, $mfaKey]) {
            $user = new User($email, $roles);
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $password));
            $manager->persist($user);

            if ($mfaKey) {
                $user->setMfaKey($mfaKey);
            }

            $this->addReference($index, $user);
        }

        $manager->flush();
    }
}

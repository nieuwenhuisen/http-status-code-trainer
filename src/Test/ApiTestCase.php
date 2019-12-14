<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as BaseApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\DataFixtures\UserFixture;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ApiTestCase extends BaseApiTestCase
{
    use FixturesTrait;

    protected static $token = [];

    protected static function createAuthenticatedClient(string $username): Client
    {
        if (!isset(self::$token[$username])) {
            $client = static::createClient();

            $response = $client->request('POST', '/authenticate', [
                'json' => [
                    'username' => $username,
                    'password' => UserFixture::DEFAULT_PASSWORD
                ],
            ]);

            $data = \json_decode($response->getContent(), true);

            self::$token[$username] = $data['token'];
        }

        return static::createClient([], ['auth_bearer' => self::$token[$username]]);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get(EntityManagerInterface::class);
    }
}

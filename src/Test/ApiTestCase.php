<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as BaseApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\DataFixtures\UserFixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class ApiTestCase extends BaseApiTestCase
{
    use FixturesTrait;

    /**
     * @var array<array<string>>
     */
    protected static array $users = [];

    protected static function createAuthenticatedClient(string $username): Client
    {
        $client = static::createClient();

        $response = $client->request('POST', '/authenticate', [
            'json' => [
                'username' => $username,
                'password' => UserFixture::DEFAULT_PASSWORD,
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        self::$users[$username] = $data;

        return static::createClient([], ['auth_bearer' => self::$users[$username]['token']]);
    }

    protected static function createAuthenticatedAndVerifiedClient(string $username, string $key): Client
    {
        $client = self::createAuthenticatedClient($username);

        $googleAuthenticator = new GoogleAuthenticator();
        $code = $googleAuthenticator->getCode('2ADFDSJF86');

        $response = $client->request('POST', '/mfa_verify', [
            'json' => [
                'code' => $code,
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        self::$users[$username] = $data;

        return static::createClient([], ['auth_bearer' => self::$users[$username]['token']]);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get(EntityManagerInterface::class);
    }

    /**
     * @param class-string $entityName
     */
    protected function getRepository(string $entityName): EntityRepository
    {
        return $this->getEntityManager()->getRepository($entityName);
    }
}

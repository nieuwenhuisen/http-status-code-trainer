<?php declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\UserFixture;
use App\Test\ApiTestCase;

final class RefreshTokenTest extends ApiTestCase
{
    public function testLoginReturnsAnRefreshToken(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $client = static::createClient();
        $response = $client->request('POST', '/authenticate', [
            'json' => [
                'username' => 'user1@user.com',
                'password' => UserFixture::DEFAULT_PASSWORD,
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('refresh_token', $data);
    }

    public function testCanRefreshAnTokenWithAnValidRefreshToken(): void
    {
        $this->loadFixtures([UserFixture::class]);

        // Create a refresh token
        static::createAuthenticatedClient('admin@admin.com');
        $refreshToken = self::$users['admin@admin.com']['refresh_token'];

        $client = static::createClient();
        $response = $client->request('POST', '/token/refresh', [
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($response->getContent(), true);

        self::assertArrayHasKey('token', $data);
        self::assertArrayHasKey('refresh_token', $data);
    }

    public function testCanNotRefreshAnTokenWithAnInvalidRefreshToken(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $refreshToken = 'somerandomrefreshtoken';

        $client = static::createClient();
        $client->request('POST', '/token/refresh', [
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }
}

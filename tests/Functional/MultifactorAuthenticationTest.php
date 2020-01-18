<?php declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response;
use App\DataFixtures\StatusCodeFixture;
use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Test\ApiTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

final class MultifactorAuthenticationTest extends ApiTestCase
{
    private function extractTokenPayloadFromResponse(Response $response): array
    {
        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        $encoder = $this->getContainer()->get(JWTEncoderInterface::class);
        return $encoder->decode($token);
    }

    public function testTokenContainsMultiFactoryAuthenticationPayload(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $client = static::createClient();
        $response = $client->request('POST', '/authenticate', [
            'json' => [
                'username' => 'user1@user.com',
                'password' => UserFixture::DEFAULT_PASSWORD,
            ],
        ]);

        $payload = $this->extractTokenPayloadFromResponse($response);

        self::assertArrayHasKey(User::MFA_ENABLED_KEY, $payload);
        self::assertArrayHasKey(User::MFA_VERIFIED_KEY, $payload);
    }

    public function testTokenContainsMultiFactoryAuthenticationPayloadAfterRefresh(): void
    {
        $this->loadFixtures([UserFixture::class]);

        // Create a refresh token
        static::createAuthenticatedClient('user4@user.com');
        $refreshToken = self::$users['user4@user.com']['refresh_token'];

        $client = static::createClient();
        $response = $client->request('POST', '/token/refresh', [
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        $payload = $this->extractTokenPayloadFromResponse($response);

        self::assertArrayHasKey(User::MFA_ENABLED_KEY, $payload);
        self::assertArrayHasKey(User::MFA_VERIFIED_KEY, $payload);
        self::assertTrue($payload[User::MFA_ENABLED_KEY]);
        self::assertFalse($payload[User::MFA_VERIFIED_KEY]);
    }

    public function testMultiFactoryAuthenticationVerify(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('user4@user.com');

        $googleAuthenticator = new GoogleAuthenticator();
        $code = $googleAuthenticator->getCode('2ADFDSJF86');

        $response = $client->request('POST', '/mfa_verify', [
            'json' => [
                'code' => $code,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $payload = $this->extractTokenPayloadFromResponse($response);

        self::assertArrayHasKey(User::MFA_ENABLED_KEY, $payload);
        self::assertArrayHasKey(User::MFA_VERIFIED_KEY, $payload);
        self::assertTrue($payload[User::MFA_ENABLED_KEY]);
        self::assertTrue($payload[User::MFA_VERIFIED_KEY]);
    }

    public function testMultiFactoryAuthenticationFailWithAnInvalidCode(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('user4@user.com');

        $client->request('POST', '/mfa_verify', [
            'json' => [
                'code' => '123456',
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    public function testRefreshAnTokenWillKeepMultiFactoryAuthenticationStatus(): void
    {
        $this->loadFixtures([UserFixture::class]);

        // Create a refresh token
        static::createAuthenticatedAndVerifiedClient('user4@user.com', '2ADFDSJF86');
        $refreshToken = self::$users['user4@user.com']['refresh_token'];

        $client = static::createClient();
        $response = $client->request('POST', '/token/refresh', [
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        $payload = $this->extractTokenPayloadFromResponse($response);

        self::assertArrayHasKey(User::MFA_ENABLED_KEY, $payload);
        self::assertArrayHasKey(User::MFA_VERIFIED_KEY, $payload);
        self::assertTrue($payload[User::MFA_ENABLED_KEY]);
        self::assertTrue($payload[User::MFA_VERIFIED_KEY]);
    }

    public function testEnableMultifactorAuthentication(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('user1@user.com');

        $client->request('POST', '/users/2/mfa_enable', ['json' => []]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testDisableMultifactorAuthentication(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('user4@user.com');

        $client->request('POST', '/users/5/mfa_disable', ['json' => []]);

        self::assertResponseIsSuccessful();
    }
}

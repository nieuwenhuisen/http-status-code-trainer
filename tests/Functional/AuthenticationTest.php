<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixture;
use App\Test\ApiTestCase;

class AuthenticationTest extends ApiTestCase
{
    public function testValidLogin(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $client = static::createClient();
        $response = $client->request('POST', '/authenticate', [
            'json' => [
                'username' => 'user1@user.com',
                'password' => UserFixture::DEFAULT_PASSWORD
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = \json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $data);
    }

    public function testInvalidLogin(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $client = static::createClient();
        $client->request('POST', '/authenticate', [
            'json' => [
                'username' => 'user1@user.com',
                'password' => 'wrong'
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEquals([
            'code' => 401,
            'message' => 'Invalid credentials.',
        ]);
    }

    public function testCouldNotAccessAPrivateResourceWithoutToken(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEquals([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCouldNotAccessAPrivateResourceWithInvalidToken(): void
    {
        $client = static::createClient([], ['auth_bearer' => 'invalidtoken']);
        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEquals([
            'code' => 401,
            'message' => 'Invalid JWT Token',
        ]);
    }

    public function testCouldNotAccessAPrivateResourceWithExpiredToken(): void
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NzY0MDc4NzYsImV4cCI6MTU3NjQwNzg4Niwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlcjFAdXNlci5jb20ifQ.F285Vr_Smk5OIJQFBFbzcrA6KD6MSt7pPVTQqdUCIROlqR2ZTR9kMoOpdF4wJYvEmliA0wa6as-lV2DAnK_jUk6LhCIglSbCDIbLNjud8rfEFkCiJoO3ffLnXpT3PuQA7UTNHIemilmtht6762evJhoCUkffSWV4w4npQHnTHo8bfJySCD3_8q7EphJqARTqeQr5OvhLPg8kvLHLRPbM_PPauX5l3cGWIXNOoYujAtDj_HiOZM9pClKFtVAymtHz2Y7UcTxDWi4KJkStT0nsAxLoV5S-dkRpokM8b4xRNux0_QMTnJQChi0Drgr9l4lR2kxflG7CVTU5e_oE3oviQp25TOlaHvGHUCCvYKjKdTH2xvG-pdSfijAWbEggjCaNN19v8vcpm9524EY-cn44409jSwfrcZaBeyRtrkxJBVzEg6ksSbpzJMtwx7DwS14uk_h1SZlReWQDgMpH2Nmj29KCIQzPBAjHcUQp_uTd9SMHgI7ssK-RmG5XyeGyN_avR4nHj9bU8HaqR8sEJP2T2Tey0Y6Ssd6X7rn21rbuwkrcy_uMk0_L3b9N_ECD59n6TYKVQAVidvUmzGDbzYgk9_c0sj4aWAWHB70Mry7gFvyEvoI296ptiq9W910sZ_vmI_0aZTZ3mu0KEDoQcUYOCeDwBY5YBZ7Av5NAwhRxd-Q';
        $client = static::createClient([], ['auth_bearer' => $token]);
        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEquals([
            'code' => 401,
            'message' => 'Expired JWT Token',
        ]);
    }
}

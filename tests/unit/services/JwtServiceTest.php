<?php

namespace tests\unit\services;

use app\models\User;
use app\services\JwtService;
use Codeception\Test\Unit;
use Yii;

class JwtServiceTest extends Unit
{
    private JwtService $jwtService;

    protected function _before()
    {
        $user = new User();
        $user->username = 'jwtuser';
        $user->email = 'jwt@example.com';
        $user->setPassword('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->save();
        $this->userId = $user->id;

        $config = [
            'secret' => 'test-secret-key',
            'issuer' => 'test-library-api',
            'audience' => 'test-library-clients',
            'ttl' => 3600,
        ];
        $this->jwtService = new JwtService($config);
    }

    public function testIssueToken()
    {
        $user = User::findOne(1);
        if (!$user) {
            $this->markTestSkipped('User not found');
        }

        $token = $this->jwtService->issueToken($user);
        verify($token)->notEmpty();
        verify($token)->string();
    }

    public function testParseValidToken()
    {
        $user = User::findOne(1);
        if (!$user) {
            $this->markTestSkipped('User not found');
        }

        $token = $this->jwtService->issueToken($user);
        $parsedUser = $this->jwtService->parseToken($token);

        verify($parsedUser)->isInstanceOf(User::class);
        verify($parsedUser->id)->equals($user->id);
    }

    public function testParseInvalidToken()
    {
        $invalidToken = 'invalid.token.here';
        $result = $this->jwtService->parseToken($invalidToken);
        verify($result)->empty();
    }
}
<?php

namespace app\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Yii;
use app\models\User;


class JwtService
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function issueToken(User $user): string
    {
        $now = time();

        $payload = [
            'iss' => $this->config['issuer'],
            'aud' => $this->config['audience'],
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $this->config['ttl'],
            'uid' => $user->id,
        ];

        return JWT::encode($payload, $this->config['secret'], 'HS256');
    }

    public function parseToken(string $token): ?User
    {
        try {
            $decoded = JWT::decode($token, new Key($this->config['secret'], 'HS256'));
            return User::findOne((int)$decoded->uid);
        } catch (ExpiredException $e) {
            Yii::warning('JWT expired: ' . $e->getMessage(), 'jwt');
        } catch (SignatureInvalidException $e) {
            Yii::warning('JWT signature invalid: ' . $e->getMessage(), 'jwt');
        } catch (\Throwable $e) {
            Yii::warning('JWT parse error: ' . $e->getMessage(), 'jwt');
        }

        return null;
    }
}
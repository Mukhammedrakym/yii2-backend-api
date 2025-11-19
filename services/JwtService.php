<?php

namespace app\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use app\models\User;


class JwtService
{
    /**
     * @param User $user
     * @return string
     */
    public static function issueToken(User $user): string
    {
        $config = Yii::$app->params['jwt'];

        $issuedAt   = time();
        $expiration = $issuedAt + $config['ttl'];

        $payload = [
            'iss' => $config['issuer'],     // кто выпустил токен
            'aud' => $config['audience'],   // кому предназначен
            'iat' => $issuedAt,             // время выпуска
            'nbf' => $issuedAt,             // не использовать раньше
            'exp' => $expiration,           // срок действия
            'uid' => $user->id,             // ID пользователя
        ];

        return JWT::encode($payload, $config['secret'], 'HS256');
    }

    /**
     * Проверяет и декодирует токен.
     * Возвращает объект User, если токен валиден, иначе null.
     *
     * @param string $token
     * @return User|null
     */
    public static function parseToken(string $token): ?User
    {
        try {
            $config = Yii::$app->params['jwt'];
            $decoded = JWT::decode($token, new Key($config['secret'], 'HS256'));
            return User::findOne((int)$decoded->uid);
        } catch (\Throwable $exception) {
            Yii::warning('JWT parse error: ' . $exception->getMessage(), 'jwt');
            return null;
        }
    }
}

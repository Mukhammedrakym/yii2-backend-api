<?php

namespace app\components;

use yii\filters\auth\HttpBearerAuth;
use app\services\JwtService;


class JwtHttpBearerAuth extends HttpBearerAuth
{
    /**
     * @inheritDoc
     */
    public function authenticate($user, $request, $response): ?IdentityInterface
    {
        $authorizationHeader = $request->getHeaders()->get('Authorization');

        if (!$authorizationHeader || !preg_match('/^Bearer\\s+(.*)$/i', $authorizationHeader, $matches)) {
            return null;
        }

        $token = $matches[1];
        $identity = JwtService::parseToken($token);

        if ($identity !== null) {
            $user->switchIdentity($identity);
            return $identity;
        }

        return null;
    }
}

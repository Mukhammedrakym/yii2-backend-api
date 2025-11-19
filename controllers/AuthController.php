<?php

namespace app\controllers;

use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use app\models\forms\LoginForm;
use app\services\JwtService;

/**
 * POST /auth/login — логин по username/password, выдаёт JWT.
 */
class AuthController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'login' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionLogin(): array
    {
        $loginForm = new LoginForm();
        $loginForm->load(\Yii::$app->request->getBodyParams(), '');

        if (!$loginForm->validate()) {
            throw new BadRequestHttpException(json_encode($loginForm->getErrors(), JSON_UNESCAPED_UNICODE));
        }

        $user = $loginForm->getUser();
        if ($user === null) {
            throw new BadRequestHttpException('Invalid credentials');
        }

        $token = JwtService::issueToken($user);

        return ['token' => $token];
    }
}

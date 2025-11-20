<?php

namespace app\controllers;

use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use app\models\LoginForm;
use app\services\JwtService;
use app\helpers\ApiResponse;
use Yii;

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
        $loginForm->load(Yii::$app->request->getBodyParams(), '');

        if (!$loginForm->validate()) {
            return ApiResponse::error($loginForm->getErrors(), 422, 'Validation failed');
        }

        $user = $loginForm->getUser();
        if ($user === null) {
            throw new UnauthorizedHttpException('Invalid credentials');
        }

        /** @var JwtService $jwtService */
        $jwtService = Yii::$app->jwtService;
        $token = $jwtService->issueToken($user);

        return ApiResponse::success(['token' => $token], 200);
    }
}
<?php

namespace app\controllers;

use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use app\models\User;
use app\components\JwtHttpBearerAuth;
use app\helpers\ApiResponse;
use Yii;

class UsersController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'view' => ['GET'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'only' => ['view'],
        ];

        return $behaviors;
    }

    public function actionCreate(): array
    {
        $user = new User();
        $user->scenario = 'register';
        $user->load(Yii::$app->request->getBodyParams(), '');

        $requestData = Yii::$app->request->getBodyParams();
        if (empty($requestData['password'])) {
            return ApiResponse::error(['password' => ['Password is required.']], 422);
        }

        $user->setPassword($requestData['password']);
        $user->auth_key = Yii::$app->security->generateRandomString();

        if (!$user->save()) {
            return ApiResponse::error($user->getErrors(), 422);
        }

        return ApiResponse::success([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
        ], 201);
    }

    public function actionView(int $id): array
    {
        $currentUser = Yii::$app->user->identity;

        if ($currentUser === null || (int)$currentUser->id !== $id) {
            throw new ForbiddenHttpException('You can view only your own profile.');
        }

        return ApiResponse::success([
            'id' => $currentUser->id,
            'username' => $currentUser->username,
            'email' => $currentUser->email,
        ]);
    }
}
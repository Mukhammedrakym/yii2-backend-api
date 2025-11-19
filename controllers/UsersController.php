<?php

namespace app\controllers;

use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use app\models\User;
use app\components\JwtHttpBearerAuth;


class UsersController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'view'   => ['GET'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'only'  => ['view'],
        ];

        return $behaviors;
    }

    public function actionCreate(): array
    {
        $requestData = \Yii::$app->request->getBodyParams();

        $user = new User();
        $user->load($requestData, '');

        if (empty($requestData['password'])) {
            throw new BadRequestHttpException('Password is required.');
        }

        $user->setPassword($requestData['password']);
        $user->auth_key = \Yii::$app->security->generateRandomString();

        if (!$user->save()) {
            throw new BadRequestHttpException(json_encode($user->getErrors(), JSON_UNESCAPED_UNICODE));
        }

        return [
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => $user->email,
        ];
    }

    public function actionView(int $id): array
    {
        $currentUser = \Yii::$app->user->identity;

        if ($currentUser === null || (int)$currentUser->id !== $id) {
            throw new ForbiddenHttpException('You can view only your own profile.');
        }

        return [
            'id'       => $currentUser->id,
            'username' => $currentUser->username,
            'email'    => $currentUser->email,
        ];
    }
}

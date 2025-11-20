<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\Book;
use app\components\JwtHttpBearerAuth;
use app\helpers\ApiResponse;
use Yii;

class BooksController extends ActiveController
{
    public $modelClass = Book::class;

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT'],
                'delete' => ['DELETE'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'only' => ['create', 'update', 'delete'],
        ];

        return $behaviors;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Book::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);
    }

    public function actionCreate(): array
    {
        $book = new Book();
        $book->load(Yii::$app->request->getBodyParams(), '');
        
        $currentUser = Yii::$app->user->identity;
        if ($currentUser === null) {
            throw new ForbiddenHttpException('Authentication required.');
        }
        $book->owner_id = $currentUser->id;

        if (!$book->save()) {
            return ApiResponse::error($book->getErrors(), 422);
        }

        return ApiResponse::success($this->serializeBook($book), 201);
    }

    public function actionUpdate(int $id): array
    {
        $book = $this->findModel($id);
        $this->checkOwnership($book);

        $book->load(Yii::$app->request->getBodyParams(), '');

        if (!$book->save()) {
            return ApiResponse::error($book->getErrors(), 422);
        }

        return ApiResponse::success($this->serializeBook($book));
    }

    public function actionDelete(int $id): void
    {
        $book = $this->findModel($id);
        $this->checkOwnership($book);

        if (!$book->delete()) {
            throw new \yii\web\ServerErrorHttpException('Failed to delete the book.');
        }

        Yii::$app->response->statusCode = 204;
    }

    protected function findModel(int $id): Book
    {
        $model = Book::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Book not found.');
        }
        return $model;
    }

    protected function checkOwnership(Book $book): void
    {
        $currentUser = Yii::$app->user->identity;
        if ($currentUser === null || (int)$book->owner_id !== (int)$currentUser->id) {
            throw new ForbiddenHttpException('You can only modify your own books.');
        }
    }

    protected function serializeBook(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'published_at' => $book->published_at,
            'description' => $book->description,
            'owner_id' => $book->owner_id,
            'created_at' => $book->created_at,
            'updated_at' => $book->updated_at,
        ];
    }
}
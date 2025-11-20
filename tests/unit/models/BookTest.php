<?php

namespace tests\unit\models;

use app\models\Book;
use app\models\User;
use Codeception\Test\Unit;
use Yii;

class BookTest extends Unit
{
    protected function _before()
    {
        Yii::$app->db->createCommand()->delete('book')->execute();
        Yii::$app->db->createCommand()->delete('user')->execute();
    }

    public function testBookValidation()
    {
        $book = new Book();
        $book->title = 'Test Book';
        $book->author = 'Test Author';

        verify($book->validate())->true();
    }

    public function testBookRequiredFields()
    {
        $book = new Book();
        verify($book->validate())->false();
        verify(array_key_exists('title', $book->errors))->true();
        verify(array_key_exists('author', $book->errors))->true();
    }

    public function testBookRelation()
    {
        $user = new User();
        $user->username = 'author';
        $user->email = 'author@example.com';
        $user->setPassword('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->save();

        $book = new Book();
        $book->title = 'Test Book';
        $book->author = 'Test Author';
        $book->owner_id = $user->id;
        $book->save();

        verify($book->user)->notEmpty();
        verify($book->user->id)->equals($user->id);
    }
}
<?php

namespace tests\unit\models;

use app\models\User;
use Codeception\Test\Unit;
use Yii;

class UserTest extends Unit
{
    private int $userId;

    protected function _before()
    {
        Yii::$app->db->createCommand()->delete('book')->execute();
        Yii::$app->db->createCommand()->delete('user')->execute();

        $user = new User();
        $user->username = 'testuser';
        $user->email = 'test@example.com';
        $user->setPassword('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->save();

        $this->userId = $user->id;
    }

    public function testFindUserById()
    {
        $user = User::findIdentity($this->userId);
        verify($user)->notEmpty();
        verify($user->username)->equals('testuser');
    }

    public function testFindUserByUsername()
    {
        $user = User::findByUsername('testuser');
        verify($user)->notEmpty();
        verify($user->email)->equals('test@example.com');

        verify(User::findByUsername('nonexistent'))->empty();
    }

    public function testSetPassword()
    {
        $user = new User();
        $user->setPassword('testpassword');

        verify($user->password)->notEquals('testpassword');
        verify($user->password)->notEmpty();
    }

    public function testValidatePassword()
    {
        $user = new User();
        $user->setPassword('testpassword');

        verify($user->validatePassword('testpassword'))->true();
        verify($user->validatePassword('wrongpassword'))->false();
    }

    public function testUsernameUnique()
    {
        $user = new User();
        $user->username = 'testuser';
        $user->email = 'another@example.com';
        $user->setPassword('password123');
        $user->auth_key = Yii::$app->security->generateRandomString();

        verify($user->validate())->false();
        verify(array_key_exists('username', $user->errors))->true();
    }
}
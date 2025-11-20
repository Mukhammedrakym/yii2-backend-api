<?php

namespace tests\unit\models;

use app\models\LoginForm;
use app\models\User;
use Codeception\Test\Unit;
use Yii;

class LoginFormTest extends Unit
{
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
    }

    public function testLoginWithCorrectCredentials()
    {
        $form = new LoginForm();
        $form->username = 'testuser';
        $form->password = 'password123';

        verify($form->validate())->true();
        $user = $form->getUser();
        verify($user)->notEmpty();
        verify($user)->instanceOf(User::class);        
        verify($user->username)->equals('testuser');
    }

    public function testLoginWrongPassword()
    {
        $form = new LoginForm();
        $form->username = 'testuser';
        $form->password = 'wrongpassword';

        verify($form->validate())->true();
        verify($form->getUser())->empty();
    }

    public function testLoginWithNonExistentUser()
    {
        $form = new LoginForm();
        $form->username = 'nonexistent';
        $form->password = 'password123';

        verify($form->validate())->true();
        verify($form->getUser())->empty();
    }

    public function testLoginFormValidation()
    {
        $form = new LoginForm();
        verify($form->validate())->false();

        $form->username = 'ab';
        $form->password = '';
        verify($form->validate())->false();
        verify(array_key_exists('username', $form->errors))->true();
        verify(array_key_exists('password', $form->errors))->true();
    }
}
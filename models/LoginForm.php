<?php

namespace app\models;

use yii\base\Model;
use app\models\User;


class LoginForm extends Model
{
    public ?string $username = null;
    public ?string $password = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 50],
        ];
    }

    public function getUser(): ?User
    {
        $user = User::findOne(['username' => $this->username]);

        if ($user === null) {
            return null;
        }

        return $user->validatePassword($this->password) ? $user : null;
    }
}

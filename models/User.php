<?php

namespace app\models;

use yii\db\ActiveRecord; 
use yii\behaviors\TimestampBehavior; 
use yii\web\IdentityInterface; 
use Yii;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['username', 'email', 'password'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 50],
            [['email'], 'email'],
            [['username', 'email'], 'unique'],
        ];
    }

    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        return null;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    public function setPassword(string $plainPassword): void
    {
        $this->password = Yii::$app->security->generatePasswordHash($plainPassword);
    }

    public function validatePassword(string $plainPassword): bool
    {
        return Yii::$app->security->validatePassword($plainPassword, $this->password);
    }
}

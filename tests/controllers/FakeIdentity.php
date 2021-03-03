<?php

namespace resttests\controllers;

use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

class FakeIdentity implements IdentityInterface
{
    public $username;
    public $id;

    /**
     * FakeIdentity constructor.
     * @param $id
     */
    public function __construct($id = 1, $username = 'username')
    {
        $this->id = $id;
        $this->username = $username;
    }

    public static function findIdentity($id)
    {
        return new self();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return new self();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return 'key';
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

}
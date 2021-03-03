<?php

namespace resttests\controllers;

use yii\base\Model;

class FakeForm extends Model
{
    public $value;

    public function rules(): array
    {
        return [
            ['value', 'required'],
            ['value', 'safe'],
        ];
    }

}
<?php

namespace api\forms;

use yii\base\Model;

class Form extends Model
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
<?php

namespace api\entities;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Phone extends ActiveRecord
{

    public function edit($phoneNumber): void
    {
        $this->phone_number = $phoneNumber;
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%phones}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

}
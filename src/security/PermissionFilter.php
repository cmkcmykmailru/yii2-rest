<?php

namespace grigor\rest\security;

class PermissionFilter extends AbstractFilter
{

    protected $can;

    public function setData(string $can)
    {
        $this->can = $can;
    }

    public function can(array $data = []): bool
    {
        if (!\Yii::$app->user->can($this->can, $data)) {
            return parent::can($data);
        }
        return false;
    }
}
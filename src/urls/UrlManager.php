<?php

namespace grigor\rest\urls;

use yii\web\UrlManager as BaseUrlManager;

class UrlManager extends BaseUrlManager
{
    public $serviceRules;

    public function init()
    {
        $this->rules = array_merge($this->rules, $this->serviceRules);
        unset($this->serviceRules);
        parent::init();
    }

}

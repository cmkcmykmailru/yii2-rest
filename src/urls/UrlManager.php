<?php

namespace grigor\rest\urls;

use yii\web\UrlManager as BaseUrlManager;

class UrlManager extends BaseUrlManager
{

    public function init()
    {
        $serviceRules = \Yii::$app->serviceMetaDataReader->readRules();
        $this->rules = array_merge($this->rules, $serviceRules);
        parent::init();
    }

}

<?php

namespace grigor\rest\urls;

use grigor\rest\controllers\RestController;
use grigor\rest\urls\installer\ServiceInstaller;
use yii\web\UrlRule;

class ServiceRule extends UrlRule
{
    public $alias;
    public $route = RestController::ROUTE;
    public $pattern;
    public $identityService;

    public function parseRequest($manager, $request)
    {
        $route = parent:: parseRequest($manager, $request);
        if (!$route) {
            return false;
        }
        $route[0] = RestController::ROUTE;

        /**@var ServiceInstaller $serviceInstaller */
        $serviceInstaller = \Yii::$app->serviceInstaller;
        $serviceInstaller->setAlias($this->alias);
        $serviceInstaller->installService($this->identityService);
        return $route;
    }

    public function createUrl($manager, $route, $params)
    {
        $old = $this->route;
        $this->route = $this->alias;
        $url = parent::createUrl($manager, $route, $params);
        $this->route = $old;
        return $url;
    }
}
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
    public $strictParams;

    public function init()
    {
        $this->strictParams = self::buildStrict($this->pattern);
        parent::init();
    }

    public static function buildStrict($pattern): array
    {
        $pattern = trim($pattern, '/');
        $strictParams = [];
        if (strpos($pattern, '<') !== false && preg_match_all('/<(\w+):?[^>]+?>/', $pattern, $matches)) {
            foreach ($matches[1] as $name) {
                $strictParams[$name] = "$name";
            }
        }
        return $strictParams;
    }

    public function parseRequest($manager, $request)
    {
        $route = parent:: parseRequest($manager, $request);
        if (empty($route)) {
            return false;
        }
        $route[0] = RestController::ROUTE;

        /**@var ServiceInstaller $serviceInstaller */
        $serviceInstaller = \Yii::$app->serviceInstaller;
        $serviceInstaller->setAlias($this->alias);
        $serviceInstaller->installService($this->identityService);
        $serviceInstaller->setStrictParams($this->strictParams);
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
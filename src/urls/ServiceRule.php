<?php

namespace grigor\rest\urls;

use grigor\rest\controllers\RestController;
use grigor\rest\urls\factory\ServiceFactory;
use yii\web\UrlRule;

class ServiceRule extends UrlRule
{

    public $alias;
    public $service;
    public $serializer;
    public $permission;
    public $route = RestController::ROUTE;
    public $pattern;

    public function init()
    {
        parent::init();
    }

    public function parseRequest($manager, $request)
    {
        $route = parent:: parseRequest($manager, $request);
        if (!$route) {
            return false;
        }
        $route[0] = RestController::ROUTE;
        $route[1]['service'] = new ServiceFactory($this->service, $this->serializer);
        return $route;
    }
}
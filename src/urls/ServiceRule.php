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
    public $permissions;
    public $route = RestController::ROUTE;
    public $pattern;
    public $response;

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
        $route[1]['service'] = new ServiceFactory(
            $this->service,
            $this->serializer,
            $this->response,
            empty($this->permissions) ? [] : $this->permissions,
            $manager->whiteList
        );
        $route[1]['alias'] = $this->alias;
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
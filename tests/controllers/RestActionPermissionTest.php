<?php

namespace resttests\controllers;

use grigor\rest\controllers\action\RestAction;
use grigor\rest\controllers\RestController;
use Yii;
use yii\helpers\FileHelper;
use yii\web\ForbiddenHttpException;

class RestActionPermissionTest extends PermissionTestCase
{
    private $controller;

    public function setUp(): void
    {
        $this->controller = $this->createController();
        FileHelper::createDirectory(Yii::getAlias('@resttests/controllers/rbac'));
    }

    public function testPermissionForbiddenHttpException()
    {
        $this->expectException(ForbiddenHttpException::class);
        $this->createUser(['user'], 2);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '11',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $result = $action->runWithParams($params);

    }

    public function testPermissionForbiddenHttpException2()
    {
        $this->expectException(ForbiddenHttpException::class);
        $this->createUser(['user','guest'], 2);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '11',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $result = $action->runWithParams($params);

    }
    public function testPermission()
    {
        $this->createUser(['admin'], 1);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '11',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $result = $action->runWithParams($params);
        self::assertEquals('40', $result);
    }

    public function testPermission2()
    {
        $this->createUser(['admin','user'], 1);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '11',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $result = $action->runWithParams($params);
        self::assertEquals('40', $result);
    }
}

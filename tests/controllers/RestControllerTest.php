<?php

namespace resttests\controllers;

use grigor\rest\controllers\action\RestAction;
use grigor\rest\controllers\RestController;
use resttests\TestCase;
use Yii;


class RestControllerTest extends TestCase
{
    private $controller;

    public function setUp(): void
    {
        $this->controller = $this->createController();
    }

    public function testBindActionParams()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '1',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = ['id' => '1'];
        $args = $this->controller->bindActionParams($action, $params);

        self::assertArrayHasKey('id', $args);
        self::assertEquals('1', $args['id']);
        self::assertCount(1, $args);


        $params = ['idd' => 'должен быть пустым'];
        $args = $this->controller->bindActionParams($action, $params);

        self::assertCount(0, $args);

        $params = [];
        $args = $this->controller->bindActionParams($action, $params);

        self::assertCount(0, $args);
    }

    public function testBindActionParamsEmptyParamsOfMethod()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method2/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '2',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = ['id' => '1'];
        $args = $this->controller->bindActionParams($action, $params);

        self::assertCount(0, $args);
    }

    public function testBindActionParamsFormParams()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method3/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '3',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = ['id' => '1'];
        $args = $this->controller->bindActionParams($action, $params);

        self::assertCount(1, $args);
        self::assertArrayHasKey('form', $args);
        self::assertEquals(FakeForm::class, get_class($args['form']));

        $params = [];
        $args = $this->controller->bindActionParams($action, $params);

        self::assertCount(1, $args);
        self::assertArrayHasKey('form', $args);
        self::assertEquals(FakeForm::class, get_class($args['form']));
    }

    public function testBindActionIgnoreParams()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '1',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = ['id' => '1'];
        $args = $this->controller->bindActionParams($action, $params);

        self::assertCount(0, $args);
    }

}

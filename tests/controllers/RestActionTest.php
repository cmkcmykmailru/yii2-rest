<?php

namespace resttests\controllers;

use yii\base\Exception;
use grigor\rest\controllers\action\RestAction;
use grigor\rest\controllers\RestController;
use resttests\TestCase;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class RestActionTest extends TestCase
{
    private $controller;

    public function setUp(): void
    {
        $this->controller = $this->createController();
    }

    public function testUniqueId()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '1',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        self::assertEquals('method1/index', $action->getUniqueId());
    }

    public function testRunWithParams()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '1',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);
        $params = ['id' => '100'];
        $result = $action->runWithParams($params);
        self::assertEquals('100', $result);
    }

    public function testRunWithParamsBadReouest()
    {
        $this->expectException(BadRequestHttpException::class);

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '1',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $action->runWithParams($params);
    }

    public function testRunWithParamsActionContext()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '4',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $result = $action->runWithParams($params);
        self::assertEquals('40', $result);

        $params = ['id' => "контекст главный"];
        $result = $action->runWithParams($params);
        self::assertEquals('40', $result);
    }

    public function testRunWithParamsCompromisingContext()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '4',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $result = $action->runWithParams($params);
        self::assertEquals('40', $result);

        $params = ['id' => "compromising"];//v1/context/demo?id=compromising
        $result = $action->runWithParams($params);
        self::assertEquals('40', $result);
    }

    public function testRunWithParams404()
    {
        $this->expectException(NotFoundHttpException::class);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '5',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $action->runWithParams($params);

    }

    public function testRunWithParams404_2()
    {
        $this->expectException(NotFoundHttpException::class);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '6',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $action->runWithParams($params);

    }

    public function testRunWithParamsStatusCode()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '7',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        $result = $action->runWithParams($params);
        self::assertEmpty($result);
        $code = Yii::$app->getResponse()->getStatusCode();
        self::assertEquals(201, $code);
    }

    public function testRunWithParamsInvalidForm()
    {

        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '8',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];
        /**@var $result FakeForm */
        $result = $action->runWithParams($params);
        self::assertEquals(FakeForm::class, get_class($result));
        self::assertCount(1, $result->errors);

        \Yii::$app->getRequest()->bodyParams = ['value' => 89611234567];
        $result = $action->runWithParams($params);
        self::assertEquals(true, is_array($result));
    }

    public function testRunWithParamsInjectMissingExeptionIfNotArgsCount()
    {
        $this->expectException(Exception::class);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\w\-]+>',
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '9',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = ['id' => 1];

        $action->runWithParams($params);
    }

    public function testRunWithParamsBadExeptionIfNotArgsCount()
    {
        $this->expectException(BadRequestHttpException::class);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\d\-]+>',//accept int
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '10',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = ['id' => "string"];//string bad

        $action->runWithParams($params);
    }

    public function testRunWithParamsEmptyExeptionIfNotArgsCount()
    {
        $this->expectException(BadRequestHttpException::class);
        $serviceInstaller = $this->getMockSreviceInstiller([
            'pattern' => '/v1/context/demo/<id:[\d\-]+>',//accept int
            'verb' => ['GET'],
            'alias' => 'method1/index',
            'class' => 'grigor\rest\urls\ServiceRule',
            'identityService' => '10',
        ]);

        $action = new RestAction(RestController::ROUTE, $this->controller, $serviceInstaller);

        $params = [];//empty bad

        $action->runWithParams($params);
    }
}

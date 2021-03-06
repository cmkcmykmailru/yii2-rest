<?php

namespace resttests;

use grigor\rest\controllers\processor\FormProcessor;
use grigor\rest\controllers\processor\FormProcessorInterface;
use grigor\rest\controllers\RestController;
use grigor\rest\urls\installer\ServiceInstaller;
use grigor\rest\urls\ServiceRule;
use PHPUnit\Framework\TestCase as BaseTestCase;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use Yii;


abstract class TestCase extends BaseTestCase
{

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->destroyApplication();
    }

    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        $params = [
            'serviceDirectoryPath' => __DIR__ . '/data/services/',
            'rulesPath' => __DIR__ . '/data/rules.php'
        ];

        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'request' => [
                    'parsers' => [
                        'application/json' => 'yii\web\JsonParser',
                    ],
                    'enableCsrfCookie' => false,
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
                'serviceMetaDataReader' => [
                    'class' => \grigor\rest\urls\installer\PhpServiceMetaDataReader::class,
                    'serviceDirectoryPath' => $params['serviceDirectoryPath'],
                    'rulesPath' => $params['rulesPath'],
                ],
                'urlManager' => [
                    'class' => \grigor\rest\urls\UrlManager::class,
                    'enablePrettyUrl' => true,
                    'enableStrictParsing' => true,
                    'showScriptName' => false,
                    'rules' => [
                    ],
                ],
            ],
        ], $config));
    }

    protected function createController()
    {
        $params = [
            'serviceDirectoryPath' => __DIR__ . '/data/services/',
            'rulesPath' => __DIR__ . '/data/rules.php'
        ];

        $controller = new RestController(RestController::ROUTE, new \yii\web\Application([
            'id' => 'testapp',
            'basePath' => __DIR__,

            'components' => [
                'request' => [
                    'parsers' => [
                        'application/json' => 'yii\web\JsonParser',
                    ],
                    'enableCsrfCookie' => false,
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
                'serviceMetaDataReader' => [
                    'class' => \grigor\rest\urls\installer\PhpServiceMetaDataReader::class,
                    'serviceDirectoryPath' => $params['serviceDirectoryPath'],
                    'rulesPath' => $params['rulesPath'],
                ],
                'urlManager' => [
                    'class' => \grigor\rest\urls\UrlManager::class,
                    'enablePrettyUrl' => true,
                    'enableStrictParsing' => true,
                    'showScriptName' => false,
                    'rules' => [
                    ],
                ],
            ],
        ]));

        $this->mockWebApplication(['controller' => $controller]);
        \Yii::$container->setSingleton(FormProcessorInterface::class, FormProcessor::class);
        return $controller;
    }

    protected function getMockSreviceInstiller(array $rule, $whiteList = false)
    {
        $serviceInstaller = new ServiceInstaller([
            'whiteList' => $whiteList,
        ]);

        $serviceInstaller->setAlias($rule['alias']);
        $serviceInstaller->installService($rule['identityService']);
        $serviceInstaller->setStrictParams(ServiceRule::buildStrict($rule['pattern']));

        return $serviceInstaller;
    }

    protected function destroyApplication()
    {
        Yii::$app = null;
        Yii::$container = new Container();
    }

    protected function debug($data)
    {
        return fwrite(STDERR, print_r($data, TRUE));
    }
}

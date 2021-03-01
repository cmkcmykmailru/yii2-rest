<?php


namespace grigorTest\rest\controllers;


use grigor\rest\controllers\RestController;
use PHPUnit\Framework\TestCase;
use yii\helpers\ArrayHelper;

class RestControllerTest extends TestCase
{
    public function testBindActionParams()
    {
        new RestController('fake', new \yii\web\Application([
            'id' => 'app',
            'basePath' => __DIR__,

            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ],
        ]));
    }
    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ],
        ], $config));
    }
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }
}
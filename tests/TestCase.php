<?php

namespace grigorTest\rest;

use PHPUnit\Framework\TestCase as BTestCase;
use Yii;
use yii\helpers\ArrayHelper;
use yiiunit\IsOneOfAssert;

/**
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends BTestCase
{
    public static $params;

    /**
     * Clean up after test case.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        $logger = Yii::getLogger();
        $logger->flush();
    }

    protected function mockRestApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapi',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'bootstrap' => [
                'log',
                grigor\rest\RestBootstrap::class,
                [
                    'class' => 'yii\filters\ContentNegotiator',
                    'formats' => [
                        'application/json' => 'json',
                        'application/xml' => 'xml',
                    ],
                ],
            ],
            'modules' => [
                'rest' => [
                    'class' => grigor\rest\Module::class,
                ],
            ],
            'controllerNamespace' => 'api\controllers',
            'components' => [
                'request' => [
                    'parsers' => [
                        'application/json' => 'yii\web\JsonParser',
                    ],
                    'enableCsrfCookie' => false
                ],
                'response' => [
                    'formatters' => [
                        'json' => [
                            'class' => 'yii\web\JsonResponseFormatter',
                            'prettyPrint' => YII_DEBUG,
                            'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                        ],
                    ],
                ],
                'user' => [
                    'identityClass' => 'common\models\User',
                    'enableAutoLogin' => false,
                    'enableSession' => false,
                ],
                'serviceInstaller' => [
                    'class' => grigor\rest\urls\installer\ServiceInstaller::class,
                    'whiteList' => false,
                ],
                'serviceMetaDataReader' => [
                    'class' => grigor\rest\urls\installer\PhpServiceMetaDataReader::class,
                    'serviceDirectoryPath' => '@api/data/static/services',
                    'rulesPath' => '@api/data/static/rules.php',
                ],
                'urlManager' => [
                    'class' => grigor\rest\urls\UrlManager::class,
                    'enablePrettyUrl' => true,
                    'enableStrictParsing' => true,
                    'showScriptName' => false,
                    'rules' => [
                        'GET ' => 'site/index',
                        'GET v1/shop' => 'site/index',
                    ],
                ],
            ],
        ], $config));
    }

    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    /**
     * Invokes a inaccessible method.
     * @param $object
     * @param $method
     * @param array $args
     * @param bool $revoke whether to make method inaccessible after execution
     * @return mixed
     * @since 2.0.11
     */
    protected function invokeMethod($object, $method, $args = [], $revoke = true)
    {
        $reflection = new \ReflectionObject($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        $result = $method->invokeArgs($object, $args);
        if ($revoke) {
            $method->setAccessible(false);
        }

        return $result;
    }

    /**
     * Sets an inaccessible object property to a designated value.
     * @param $object
     * @param $propertyName
     * @param $value
     * @param bool $revoke whether to make property inaccessible after setting
     * @since 2.0.11
     */
    protected function setInaccessibleProperty($object, $propertyName, $value, $revoke = true)
    {
        $class = new \ReflectionClass($object);
        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        if ($revoke) {
            $property->setAccessible(false);
        }
    }

    /**
     * Gets an inaccessible object property.
     * @param $object
     * @param $propertyName
     * @param bool $revoke whether to make property inaccessible after getting
     * @return mixed
     */
    protected function getInaccessibleProperty($object, $propertyName, $revoke = true)
    {
        $class = new \ReflectionClass($object);
        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $result = $property->getValue($object);
        if ($revoke) {
            $property->setAccessible(false);
        }

        return $result;
    }

}

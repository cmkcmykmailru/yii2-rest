<?php

namespace grigor\rest;

use grigor\rest\controllers\Form;
use grigor\rest\controllers\processor\FormProcessor;
use grigor\rest\controllers\processor\FormProcessorInterface;
use Yii;
use yii\base\BootstrapInterface;
use yii\di\Container;
use yii\di\Instance;

class RestBootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        $container = \Yii::$container;
        $container->setSingleton(FormProcessorInterface::class, FormProcessor::class);

    }
}


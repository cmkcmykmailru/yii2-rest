<?php

namespace grigor\rest\controllers\processor;

use yii\base\Model;
use yii\web\Request;

interface FormProcessorInterface extends ProcessorInterface
{
    public const EVENT_BEFORE_LOAD = 'beforeLoadForm';
    public const EVENT_AFTER_LOAD = 'afterLoadForm';

    public function load(Model $form, &$args, Request $request): void;
}
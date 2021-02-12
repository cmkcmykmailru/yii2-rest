<?php

namespace grigor\rest\controllers\processor;

use yii\base\Model;
use yii\web\Request;

interface FormProcessorInterface
{
    public function load(Model $form, &$args, Request $request): void;
}
<?php

namespace grigor\rest\controllers\processor;

use yii\base\Model;

interface ProcessorInterface
{
    public function getForm(): ?Model;
}
<?php

namespace grigor\rest\controllers\processor;

use yii\base\Model;
use yii\web\Request;

abstract class AbstractFormProcessor implements FormProcessorInterface
{

    public function load(Model $form, &$args, Request $request): void
    {
        $this->form = $this->loadForm($form, $request);
        if (!empty($this->form)) {
            $args[] = $this->form;
        }
    }

    abstract function loadForm(Model $form, Request $request): ?Model;

}
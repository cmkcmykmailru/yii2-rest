<?php

namespace grigor\rest\controllers\processor;

use yii\base\Model;
use yii\web\Request;

abstract class AbstractFormProcessor implements FormProcessorInterface
{

    public function load(Model $form, string $name, &$args, Request $request): void
    {
        $this->form = $this->loadForm($form, $request);
        if (!empty($this->form)) {
            $args[$name] = $this->form;
        }
    }

    abstract protected function loadForm(Model $form, Request $request): ?Model;

}
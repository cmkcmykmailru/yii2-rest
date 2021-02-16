<?php

namespace grigor\rest\controllers\processor;

use yii\base\Model;
use yii\web\Request;

class FormProcessor extends AbstractFormProcessor
{

    /**
     * @param Model $form
     * @param Request $request
     * @return Model|null
     * @throws \yii\base\InvalidConfigException
     */
    protected function loadForm(Model $form, Request $request): ?Model
    {
        $form->load($request->getBodyParams(), '');
        return $form;
    }
}
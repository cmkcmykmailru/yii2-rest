<?php

namespace grigor\rest\controllers\action;

use grigor\rest\controllers\processor\ProcessorInterface;
use grigor\rest\urls\factory\ServiceFactory;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class RestAction extends Action
{

    public $service;
    public $method;
    public $serviseFactory;
    private $formPocessor;

    public function runWithParams($params)
    {
        /** @var ServiceFactory $serviceFactory */
        $this->serviseFactory = $serviceFactory = $params['service'];

        $this->service = $serviceFactory->createService();
        $method = $serviceFactory->getMethod();

        $args = $this->controller->bindActionParams($this, $params);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }

        $form = empty($this->formPocessor) ? null : $this->formPocessor->getForm();
        if ($form !== null && !$form->validate()) {
            return $form;
        }

        try {
            $result = call_user_func_array([$this->service, $method], $args);
        } catch (\DomainException $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }

        return $result;
    }

    public function bindProcessor(ProcessorInterface $processor)
    {
        $this->formPocessor = $processor;
    }
}
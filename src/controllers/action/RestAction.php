<?php

namespace grigor\rest\controllers\action;

use DomainException;
use grigor\rest\exception\NotFoundException;
use grigor\rest\urls\factory\ServiceInstaller;
use Yii;
use yii\base\Action;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class RestAction extends Action
{

    public $service;
    public $method;
    private $form;
    private $alias;

    public function __construct($id, $controller, $config = [])
    {
        $this->alias =  \Yii::$app->serviceInstaller->getAlias();
        parent::__construct($id, $controller);
    }

    public function getUniqueId()
    {
        return $this->alias;
    }

    public function runWithParams($params)
    {
        /** @var ServiceInstaller $serviceInstaller */
        $serviceInstaller =  \Yii::$app->serviceInstaller;
        $this->service = $serviceInstaller->createService();
        $method = $serviceInstaller->getMethod();

        $args = $this->controller->bindActionParams($this, $params);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }
        $verifier = $serviceInstaller->getVerifier();

        if ($verifier !== null) {
            $verifier->fireForbiddenHttpExceptionIfNotPermission([
                'arguments' => $args,
                'route' => $this->alias
            ]);
        }

        if (!empty($this->form) && !$this->form->validate()) {
            return $this->form;
        }

        try {
            $result = call_user_func_array([$this->service, $method], $args);
        } catch (NotFoundException $e) {
            $this->fireNotFoundHttpException($e->getMessage());
        } catch (DomainException $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }

        if (!$serviceInstaller->isEmptyStatusCode()) {
            Yii::$app->getResponse()->setStatusCode($serviceInstaller->getStatusCode());
            return empty($result) ? [] : $result;
        }

        if ($result === null) {
            $this->fireNotFoundHttpException();
        }

        return $result;
    }

    private function fireNotFoundHttpException($message = 'The requested page does not exist.')
    {
        throw new NotFoundHttpException($message);
    }

    public function setForm(Model $form)
    {
        $this->form = $form;
    }
}
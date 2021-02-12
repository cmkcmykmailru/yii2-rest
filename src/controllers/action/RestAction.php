<?php

namespace grigor\rest\controllers\action;

use DomainException;
use grigor\rest\exception\NotFoundException;
use grigor\rest\security\PermissionFilter;
use grigor\rest\security\Verifier;
use grigor\rest\urls\factory\ServiceFactory;
use Yii;
use yii\base\Action;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class RestAction extends Action
{

    public $service;
    public $method;
    public $serviceFactory;
    private $form;

    public function runWithParams($params)
    {
        /** @var ServiceFactory $serviceFactory */
        $this->serviceFactory = $serviceFactory = $params['service'];
        $alias = $params['alias'];
        $this->service = $serviceFactory->createService();
        $method = $serviceFactory->getMethod();

        $args = $this->controller->bindActionParams($this, $params);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }
        $verifier = $serviceFactory->getVerifier();

        if ($verifier !== null) {
            $verifier->fireForbiddenHttpExceptionIfNotPermission([
                'arguments' => $args,
                'route' => $alias
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

        if (!$serviceFactory->isEmptyStatusCode()) {
            Yii::$app->getResponse()->setStatusCode($serviceFactory->getStatusCode());
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
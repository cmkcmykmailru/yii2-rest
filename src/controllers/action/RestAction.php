<?php

namespace grigor\rest\controllers\action;

use DomainException;
use grigor\rest\exception\NotFoundException;
use grigor\rest\urls\installer\ServiceInstaller;
use Yii;
use yii\base\Action;
use yii\base\Exception;
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
        $this->alias = \Yii::$app->serviceInstaller->getAlias();
        parent::__construct($id, $controller);
    }

    public function getUniqueId()
    {
        return $this->alias;
    }

    public function runWithParams($params)
    {
        /** @var ServiceInstaller $serviceInstaller */
        $serviceInstaller = \Yii::$app->serviceInstaller;
        $this->service = $serviceInstaller->createService();
        $method = $serviceInstaller->getMethod();

        $args = $this->controller->bindActionParams($this,$params);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }

        $contextParams = [];
        $context = $serviceInstaller->getActionContext();
        if ($context !== null) {
            $cParams = $context->getParams($args);
            $contextParams = $cParams === null ? [] : $cParams;
        }

        $this->fireExeptionIfNotArgsCount($contextParams);
        $args = array_merge($args, array_values($contextParams));

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

    private function fireExeptionIfNotArgsCount($contextParams)
    {
        $keysContext = array_keys($contextParams);
        $missing = $this->controller->missing;
        $injectMissing = $this->controller->injectMissing;

        if (!empty($injectMissing) && count($im = array_diff($injectMissing, $keysContext)) > 0) {
            throw new Exception('Could not load required service: ' . implode(',', $im));
        }

        if (!empty($missing) && count($m = array_diff($missing, $keysContext)) > 0) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => implode(', ', $m),
            ]));
        }
    }

    public function setForm(Model $form)
    {
        $this->form = $form;
    }
}
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
    public $serviceInstaller;
    private $form;
    private $alias;

    public function __construct($id, $controller, ServiceInstaller $serviceInstaller, $config = [])
    {
        $this->serviceInstaller = $serviceInstaller;
        $this->alias = $this->serviceInstaller->getAlias();
        $this->service = $serviceInstaller->createService();
        parent::__construct($id, $controller, $config);
    }

    public function getUniqueId()
    {
        return $this->alias;
    }

    public function runWithParams($params)
    {
        /** @var ServiceInstaller $serviceInstaller */
        $serviceInstaller = $this->serviceInstaller;

        $method = $serviceInstaller->getMethod();

        $args = $this->controller->bindActionParams($this, $params);

        $contextParams = [];
        $context = $serviceInstaller->getActionContext();
        if ($context !== null) {
            $cParams = $context->getParams($args);
            $contextParams = $cParams === null ? [] : $cParams;
        }

        $this->fireExeptionIfNotArgsCount($contextParams);
        $args = array_merge($args, $contextParams);

        $verifier = $serviceInstaller->getVerifier();

        $argValues = array_values($args);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $argValues;
        }

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
            $result = call_user_func_array([$this->service, $method], $argValues);
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
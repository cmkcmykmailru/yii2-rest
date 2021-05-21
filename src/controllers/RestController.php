<?php

namespace grigor\rest\controllers;

use grigor\rest\controllers\action\RestAction;
use grigor\rest\controllers\processor\FormProcessorInterface;
use grigor\rest\serializer\Serializer;
use yii\base\Model;
use yii\db\ActiveRecordInterface;
use yii\rest\Controller;
use Yii;
use yii\base\Exception;
use yii\web\BadRequestHttpException;

class RestController extends Controller
{
    public const ROUTE = 'rest/rest/index';
    public $serializer = Serializer::class;
    public $missing = [];
    public $injectMissing = [];

    public function createAction($id)
    {
        return new RestAction($id, $this, \Yii::$app->serviceInstaller);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if (!empty(\Yii::$app->serviceInstaller->behaviors)) {
            $callback = \Yii::$app->serviceInstaller->behaviors;
            return $callback($this, $behaviors);
        }
        return $behaviors;
    }

    public function bindActionParams($action, $params)
    {
        $serviceInstaller = $action->serviceInstaller;
        $method = new \ReflectionMethod($action->service, $serviceInstaller->getMethod());

        $methodParams = $method->getParameters();

        $strict = $serviceInstaller->getStrictParams();
        $params = array_filter($params, function ($key) use ($strict) {
            return in_array($key, $strict);
        }, ARRAY_FILTER_USE_KEY);

        $args = [];
        $actionParams = [];
        $requestedParams = [];
        foreach ($methodParams as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                $isValid = true;
                if (PHP_VERSION_ID >= 80000) {
                    $isArray = ($type = $param->getType()) instanceof \ReflectionNamedType && $type->getName() === 'array';
                } else {
                    $isArray = $param->isArray();
                }
                if ($isArray) {
                    $params[$name] = (array)$params[$name];
                } elseif (is_array($params[$name])) {
                    $isValid = false;
                } elseif (
                    PHP_VERSION_ID >= 70000 &&
                    ($type = $param->getType()) !== null &&
                    $type->isBuiltin() &&
                    ($params[$name] !== null || !$type->allowsNull())
                ) {
                    $typeName = PHP_VERSION_ID >= 70100 ? $type->getName() : (string)$type;
                    switch ($typeName) {
                        case 'int':
                            $params[$name] = filter_var($params[$name], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                            break;
                        case 'float':
                            $params[$name] = filter_var($params[$name], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
                            break;
                        case 'bool':
                            $params[$name] = filter_var($params[$name], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                            break;
                    }
                    if ($params[$name] === null) {
                        $isValid = false;
                    }
                }
                if (!$isValid) {
                    throw new BadRequestHttpException(Yii::t('yii', 'Invalid data received for parameter "{param}".', [
                        'param' => $name,
                    ]));
                }
                $args[$name] = $actionParams[$name] = $params[$name];
                unset($params[$name]);
            } elseif (PHP_VERSION_ID >= 70100 && ($type = $param->getType()) !== null && !$type->isBuiltin()) {
                try {
                    $this->injectedParams($type, $name, $args, $requestedParams, $action);
                } catch (Exception $e) {
                    throw new \RuntimeException($e->getMessage(), 0, $e);
                }
            } elseif ($param->isDefaultValueAvailable()) {
                $args[$name] = $actionParams[$name] = $param->getDefaultValue();
            } else {
                if (isset($strict[$name])) {
                    $this->missing[] = $name;
                } else {
                    $this->injectMissing[] = $name;
                }
            }
        }

        $this->actionParams = $actionParams;

        // We use a different array here, specifically one that doesn't contain service instances but descriptions instead.
        if (\Yii::$app->requestedParams === null) {
            \Yii::$app->requestedParams = array_merge($actionParams, $requestedParams);
        }

        return $args;
    }

    protected function injectedParams(\ReflectionType $type, $name, &$args, &$requestedParams, $action)
    {
        $typeName = $type->getName();
        $parents = class_parents($typeName);
        $impls = class_implements($typeName);

        if (isset($parents[Model::class]) && !isset($impls[ActiveRecordInterface::class])) {
            /** @var Model $form */
            $form = \Yii::$container->has($typeName) ? \Yii::$container->get($typeName) : \Yii::createObject($typeName);
            $formProcessor = \Yii::$container->get(FormProcessorInterface::class);
            $formProcessor->load($form, $name, $args, Yii::$app->request);
            $requestedParams[$name] = "FormProcessor: $typeName \$$name";
            $action->setForm($form);
            return;
        }

        if ($type->allowsNull()) {
            $args[$name] = null;
            $requestedParams[$name] = "Unavailable service: $name";
            return;
        }
        $this->injectMissing[] = $name;
    }

    protected function serializeData($data)
    {
        return Yii::createObject([
            'class' => $this->serializer,
            'serviceInstaller' => \Yii::$app->serviceInstaller
        ])->serialize($data);
    }
}
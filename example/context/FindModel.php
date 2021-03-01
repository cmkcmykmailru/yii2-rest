<?php

namespace api\context;

use grigor\rest\controllers\action\ActionContextInterface;
use yii\web\NotFoundHttpException;

class FindModel implements ActionContextInterface
{
    public function getParams($args): ?array
    {
        $id = $args[0];
        /**
         * http://api.user.local/v1/context/demo/2222222 то будет 404
         */
        if ((int)$id === 2222222) {
            throw new NotFoundHttpException('Page not found.');
        }
        return ['some' => 2];
    }
}
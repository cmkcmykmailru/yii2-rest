<?php

namespace resttests\controllers;

use grigor\rest\controllers\action\ActionContextInterface;

class FakeActionContext implements ActionContextInterface
{
    public function getParams($args): ?array
    {
        return ['id' => $args['id']];
    }
}
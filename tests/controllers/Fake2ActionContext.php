<?php

namespace resttests\controllers;

use grigor\rest\controllers\action\ActionContextInterface;

class Fake2ActionContext implements ActionContextInterface
{
    public function getParams($args): ?array
    {
        return ['id' => '40'];
    }
}
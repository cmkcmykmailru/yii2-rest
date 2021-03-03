<?php

namespace resttests\controllers;

use grigor\rest\controllers\action\ActionContextInterface;

class Fake3ActionContext implements ActionContextInterface
{
    public function getParams($args): ?array
    {
        return [];
    }
}
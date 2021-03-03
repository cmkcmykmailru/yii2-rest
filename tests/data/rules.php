<?php return [
    0 => [
        'pattern' => '/v1/context/demo/<id:[\w\-]+>',
        'verb' => ['GET'],
        'alias' => 'method1/index',
        'class' => 'grigor\rest\urls\ServiceRule',
        'identityService' => '1',
    ],
    1 => [
        'pattern' => '/v1/shop/phones/<id:[\w\-]+>',
        'verb' => ['GET'],
        'alias' => 'phones/view',
        'class' => 'grigor\rest\urls\ServiceRule',
        'identityService' => '2',
    ],
    2 => [
        'pattern' => '/v1/shop/phones',
        'verb' => ['GET'],
        'alias' => 'phones/index',
        'class' => 'grigor\rest\urls\ServiceRule',
        'identityService' => '3',
    ],
    3 => [
        'pattern' => '/v1/shop/phones',
        'verb' => ['POST'],
        'alias' => 'phones/create',
        'class' => 'grigor\rest\urls\ServiceRule',
        'identityService' => '4',
    ],

    5 => ['pattern' => '/v1/shop/phones/<id:[\w\-]+>',
        'verb' => ['PUT'],
        'alias' => 'phones/update',
        'class' => 'grigor\rest\urls\ServiceRule',
        'identityService' => '5',
    ],
];
yii2-rest
=====
Работа с RESTful. Позволяет любой класс использовать в качестве action.
Сейчас проект еще не закончен. Планируется посредством чтения аннотаций в файлах проекта
генерировать некую конфигурацию api. 

[Scanner](https://github.com/cmkcmykmailru/scanner) - будет использоваться для сканирования файлов проекта.

Установка
------------

Предпочтительный способ установки этого расширения - через [composer](http://getcomposer.org/download/).

Запустите команду

```
php composer.phar require --prefer-dist grigor/yii2-rest "*"
```

или добавьте в composer.json

```
"grigor/yii2-rest": "*",
```

Настройка
-----
Скопируйте папку frontend или backend в корень проекта и переименуйте как вам нравится у меня будет api. И не забудьте добавить 
в файл common/config/bootstrap.php такую трочку Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');

Файл api/config/main.php может быть таким:

```php
<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        grigor\rest\RestBootstrap::class,
        [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => 'json',
                'application/xml' => 'xml',
            ],
        ],
    ],
    'modules' => [
        'rest' => [
            'class' => grigor\rest\Module::class,
        ],
    ],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfCookie' => false
        ],
        'response' => [
            'formatters' => [
                'json' => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'serviceInstaller' => [
            'class' => grigor\rest\urls\installer\ServiceInstaller::class,
            'whiteList' => false,
        ],
        'serviceMetaDataReader' => [
            'class' => grigor\rest\urls\installer\PhpServiceMetaDataReader::class,
            'serviceDirectoryPath' => '@api/data/static/services',
            'rulesPath' => '@api/data/static/rules.php',
        ],
        'urlManager' => [
            'class' => grigor\rest\urls\UrlManager::class,
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'GET ' => 'site/index',
                'GET v1/shop' => 'site/index',
            ],
        ],
    ],
    'params' => $params,
];
```
Параметр

'rulesPath' => '@api/data/static/rules.php', указывает на файл правил для роутов

```php
<?php

return [
    0 => [
        'pattern' => "v1/shop/products/<id:[\w\-]+>", // url по правилам Yii2 за исключением <controller...> (потом будет кидать исключение на такую настройку)
        'verb' => ['GET'], //метод по которому сее деяние будет доступно
        'alias' => 'product/index', // т.к. контроллеров при таком подходе нет, а для генерации url требуется роут, то прописываем любой не существующий
        'class' => 'grigor\rest\urls\ServiceRule', //правило (наследник UrlRule)
        'identityService' => 'eca98246-8562-4edb-8d5d-07c65558d9da' //идентификатор настройки для данного роута
    ],
    ... и еще куча правил
];
```


Параметр 

'serviceDirectoryPath' => '@api/data/static/services', - указывает на папку где лежат настройки action (ими могут быть любые классы)
Настройки могут находится и в базе и файлах, зависит от реализации ServiceMetaDataReaderInterface 
Пример настройки (одна настройка один файл) , если планируется хранить в файла то название может быть таким eca98246-8562-4edb-8d5d-07c65558d9da.php да вообще любым
```php
<?php
return [
   /** могут использоваться для формирования белых списков уникальных для конкретно этого action */
    //  'permissions' => ["guest", "ruleName1", "ruleName2"],
    'service' => [
        'class' => 'api\project\SomeClass', //экземпляр класса который будет отрабатывать
        'method' => 'func' //метод который будет отрабатывать 
    ],
    'serializer' => 'api\serialize\SerializeProduct',
    'context' => 'api\context\FindModel', // ограничитель области действия - можно так сказать, (типа как findModel)
    /** если action предполагает просто какое то действие и после выполнения должен вернуть какой нибудь статус. */
   // 'response' => 201,
];
```

Параметр (не обязательный) -
'serializer' => 'api\serialize\SerializeProduct',

если не хочется заморачиваться с fields() и extraFields() можно использовать свой сериалайзер. Он должен иметь один метод обязательный
__invoke(...); таким образом можно сериализовать как одну сущность, так и весь DataProviderInterface


```php

 public function __invoke(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
        ];
    }
```


Параметр (не обязательный) -
'context' => 'api\context\FindModel',  ограничитель условно может быть таким:

```php

class FindModel implements ActionContextInterface
{

    public function getParams($args): ?array
    {
        $id = $args[0];
        if ($id !== '...') {
            throw new NotFoundHttpException('Page not found.');
        }
        return ['id' => $id];
    }
}
```
Вернуть он может массив с недостающими в отрабатывающем методе параметрами. Например у нас есть метод public function getProfile(string $id)
а api-шка должна отдавать профиль текущего юзера. Т.е. для пользователя системы нет параметра id получается url примерно такой /v2/user/profile метод GET и все, но мы используем public function getProfile(string $id) где нужно передать id user-а в данном случае текущего.
```php

class FindModel implements ActionContextInterface
{

    public function getParams($args): ?array
    {
        return ['id' =>  \Yii::$app->user->id];// id тут сопоставится с параметром метода getProfile(string $id), потому называться должен также
        //причем, если в url будет добавлен параметр id как то так  /v2/user/profile/какойтоid (404) или /v2/user/profile?id=какойтоid (проигнорирован)  - он будет проигнорирован
    }
}
```

Для всего этого дела удачно подходят сервисы и репозитории.
И скоро система будет использовать аннотации в качестве заместителей файлов конфигураций, на их основе будут генерироваться настройки и записываться либо в файлы, либо в базу, это кому как нравится, в последнем случае можно легко организовать админку для всего этого дела.
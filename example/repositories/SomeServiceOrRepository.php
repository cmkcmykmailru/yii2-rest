<?php

namespace api\repositories;

use api\entities\Phone;
use api\forms\Form;
use Ramsey\Uuid\Uuid;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use grigor\generator\annotation as API;

/**
 * Class SomeServiceOrRepository
 * Все названия методов подсказал фонарь.)))
 * Настройки тоже...
 * в methods={"POST"}, можно перечислять через запятую methods={"POST,GET и тд."},
 * @package api\serialize
 */
class SomeServiceOrRepository
{
    /**
     * @API\Route(
     *     url="/v1/context/demo/<id:[\w\-]+>",
     *     methods={"GET"},
     *     alias="context/index",
     * )
     * @API\Context("api\context\FindModel")
     * @API\Response(statusCode="201")
     * @param string $id
     * @param Form $model
     * @return integer
     * @throws InvalidConfigException
     */
    public function getDemoContextMethod(string $id, Form $model, int $some)
    {
        return [$some];
    }

    /**
     * @API\Route(
     *     url="/v1/shop/phones/<id:[\w\-]+>",
     *     methods={"GET"},
     *     alias="phones/view"
     * )
     * @API\Serializer("api\serializers\SerializePhone")
     * @return Phone
     */
    public function getPhone(string $id): Phone
    {
        $phone = Phone::findOne(['id' => $id]);
        if (empty($phone)) {
            throw new \DomainException('нету такого');
        }
        return $phone;
    }

    /**
     * @API\Route(
     *     url="/v1/shop/phones",
     *     methods={"GET"},
     *     alias="phones/index"
     * )
     * @API\Serializer("api\serializers\SerializePhone")
     * @return DataProviderInterface
     */
    public function getAllPhones(): DataProviderInterface
    {
        $query = Phone::find();
        return $this->getProvider($query);
    }

    /**
     * @API\Route(
     *     url="/v1/shop/phones",
     *     methods={"POST"},
     *     alias="phones/create"
     * )
     * @API\Response(statusCode="201")
     * @param Form $model
     * @return void
     */
    public function createPhone(Form $model): void
    {
        (new Phone([
            'id' => Uuid::uuid4()->toString(),
            'phone_number' => $model->value
        ]))->save();
    }

    /**
     * @API\Route(
     *     url="/v2/shop/phones",
     *     methods={"POST"},
     *     alias="v2/phones/create"
     * )
     * @API\Response(statusCode="201")
     * @param Form $model
     * @return Phone
     */
    public function createAndReturnPhone(Form $model): Phone
    {
        $phone = new Phone([
            'id' => Uuid::uuid4()->toString(),
            'phone_number' => $model->value
        ]);
        $phone->save();
        return $phone;
    }

    /**
     * @API\Route(
     *     url="/v1/shop/phones/<id:[\w\-]+>",
     *     methods={"PUT"},
     *     alias="phones/update"
     * )
     * @API\Response(statusCode="202")
     * @API\Serializer("api\serializers\SerializePhone")
     * @param Form $model
     * @return Phone
     * @throws InvalidConfigException
     */
    public function updatePhone(string $id, Form $model):Phone
    {
        $phone = Phone::findOne(['id' => $id]);
        $phone->phone_number = $model->value;
        if (!$phone->save()) {
            throw new \DomainException('Error');
        }
        return $phone;
    }

    private function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                    ],
                    'phone_number' => [
                        'asc' => ['phone_number' => SORT_ASC],
                        'desc' => ['phone_number' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSizeLimit' => [15, 100],
            ]
        ]);
    }
}
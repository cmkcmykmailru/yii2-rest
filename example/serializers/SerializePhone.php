<?php

namespace api\serializers;

use api\entities\Phone;

class SerializePhone
{
    public function __invoke(Phone $phone): array
    {
        return [
            'id' => $phone->id,
            'number' => $phone->phone_number,
        ];
    }
}
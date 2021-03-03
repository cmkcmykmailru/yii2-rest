<?php

namespace resttests\controllers;

use grigor\rest\exception\NotFoundException;


class FakeService
{
    public function method1($id)
    {
        return $id;
    }

    public function method2()
    {

    }

    public function method3(FakeForm $form)
    {
        return $form;
    }

    public function method4()
    {
        throw new NotFoundException('Not found.');
    }

    public function method5()
    {
        return null;
    }

    public function method6(FakeForm $form)
    {
        return [1, 2, 3];
    }

    public function method7($id, $idd, FakeForm $form)
    {
        return [1, 2, 3];
    }

    public function method8(int $id,  FakeForm $form)
    {
        return [1, 2, 3];
    }
}
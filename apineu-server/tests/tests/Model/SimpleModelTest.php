<?php

namespace Neuedev\Apineu\Tests\Model;

use Neuedev\Apineu\Model\SimpleModel;
use PHPUnit\Framework\TestCase;

class SimpleModelTest extends TestCase
{
    public function test_model()
    {
        $model = new SimpleModel();

        $json = $model->jsonSerialize();

        $this->assertEquals([], $json);
    }
}

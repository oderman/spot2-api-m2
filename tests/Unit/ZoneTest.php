<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Http\Controllers\ZoneController;

class ZoneTest extends TestCase
{
   
    public function testQuatity()
    {
        $result = ZoneController::validateQuantity(1);
        $this->assertTrue($result);

        $result = ZoneController::validateQuantity(0);
        $this->assertFalse($result);
    }
}

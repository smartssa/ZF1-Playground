<?php

class TestControllerTest extends PG_Test_PHPUnit_ControllerTestCase
{
    
    function testIndex() {
        $this->dispatch('/test');
        $this->assertResponseCode(200);
    }
}


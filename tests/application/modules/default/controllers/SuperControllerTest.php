<?php

class SuperControllerTest extends PG_Test_PHPUnit_ControllerTestCase {
    
    function testIndexAction() {
        $this->dispatch('/super');
        $this->assertResponseCode(200);
    }
}
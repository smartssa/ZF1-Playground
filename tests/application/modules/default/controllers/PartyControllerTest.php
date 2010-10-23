<?php

class PartyControllerTest extends PG_Test_PHPUnit_ControllerTestCase
{

    function setUp() {
        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->dispatch('/party');
        // test the redirect?
        $this->assertRoute('default');
    }

    public function testFourOhFourAction() {
        $this->dispatch('/idonotexist');
        $this->assertResponseCode(404);
        $this->assertRoute('default');
    }

}


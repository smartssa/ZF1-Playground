<?php
class PG_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->frontController->setParam('bootstrap', Zend_Registry::get('bootstrap'));
        $this->frontController->addModuleDirectory('../application/modules');
    }
}
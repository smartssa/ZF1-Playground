<?php

/**
 * Enter description here ...
 * @author darrylc
 *
 */
class PartyController extends PG_Controller_Action
{

    /* (non-PHPdoc)
     * @see library/PG/Controller/PG_Controller_Action::init()
     */
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
    }

    /**
     * Enter description here ...
     */
    public function indexAction()
    {

    }

    /**
     * Sample test harness
     */
    public function testAction() {

        $this->log->info('Party Test Start ' . $this->_request->getControllerName());

        $this->_helper->actionStack('test-process', 'party');
        $this->_helper->actionStack('index', 'super');
    }

    /**
     * Test harness post-process
     */
    public function testProcessAction() {

        $this->log->info('Test Test ' . $this->_request->getActionName());
        $this->log->debug(print_r($this->_response->getBody(), true));
        Zend_Debug::dump($this->view);
    }

}


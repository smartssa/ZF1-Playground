<?php

/**
 * Enter description here ...
 * @author darrylc
 */
class PG_Controller_Action extends Zend_Controller_Action
{

    /**
     * Logging object
     * @var Zend_Log
     */
    protected $log;

    /* (non-PHPdoc)
     * @see Zend/Controller/Zend_Controller_Action::init()
     */
    public function init() {
        $this->log = $this->getInvokeArg('bootstrap')->getResource('Log');

        // load translations
        /* Initialize action controller here */
        if ($this->getRequest()->isXMLHttpRequest()) {
            $this->_helper->layout()->setLayout('blank');
            $logger = $this->getInvokeArg('bootstrap')->getResource('Log');
            $logger->debug('AJAX Call');
        }

    }

    /* (non-PHPdoc)
     * @see Zend/Controller/Zend_Controller_Action::preDispatch()
     */
    public function preDispatch() {

        $this->log->debug('preDispatch() - ' . __FILE__ . ' ' . $this->_request->getControllerName());
    }

    /* (non-PHPdoc)
     * @see Zend/Controller/Zend_Controller_Action::postDispatch()
     */
    public function postDispatch() {
        $this->log->debug('postDispatch() - ' . __FILE__ . ' ' . $this->_request->getControllerName());
    }


}
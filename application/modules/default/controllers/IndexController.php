<?php

/**
 * Enter description here ...
 * @author darrylc
 *
 */
class IndexController extends PG_Controller_Action
{

    /* (non-PHPdoc)
     * @see Zend/Controller/Zend_Controller_Action::init()
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
        $this->view->date = new Zend_Date();
    }

    public function navigationAction()
    {

    }

    public function loggingAction()
    {

    }

    public function cachingAction()
    {

    }
    
    public function sessionsAction()
    {
        
    }
    
}


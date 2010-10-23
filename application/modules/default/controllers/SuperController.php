<?php

/**
 * Enter description here ...
 * @author darrylc
 *
 */
class SuperController extends PG_Controller_Action
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
     * useless index
     */
    public function indexAction()
    {
        // action body
        $this->log->info('Super Index');
        $this->log->debug(print_r($this->_request->getParams(), true));
        $this->view->woot = $this->_request->getParam('woot');
    }


}


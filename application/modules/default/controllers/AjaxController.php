<?php

class AjaxController extends PG_Controller_Action {

    public function init() {

        $this->_helper->layout()->setLayout('blank');
        parent::init();

    }
    
    public function getNavAction() {
        // return the nav branch for the requested string.
    }
}
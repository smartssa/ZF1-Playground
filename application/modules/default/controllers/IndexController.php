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

    public function badgesAction()
    {
        $badges = new DEC_Badges(1);
        $this->view->badges = $badges;
    }

    public function eventTriggerAction()
    {
        // ajax shit.
        $event = new DEC_Badges_Events();
        $event->triggerEvent(1, (int)$this->_request->getParam('id'), false, $this->_request->getParam('extra'));
    }

    public function checkBadgesAction()
    {
        // return a badges thing
        $badges = new DEC_Badges(1);
        Zend_Debug::dump($badges->getRecentBadges());
        Zend_Debug::dump($badges->getPreviousBadges());
        Zend_Debug::dump($badges->getAllBadges());
        
        $this->view->badges = $badges->getRecentBadges();
    }
}


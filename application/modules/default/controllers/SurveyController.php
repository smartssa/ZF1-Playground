<?php

/**
 * Enter description here ...
 * @author darrylc
 *
 */
class SurveyController extends PG_Controller_Action
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
        $userId = 1; // to be gotten from auth.
        $surveys = new DEC_Survey($userId, $this->view->url(array('controller' => 'survey', 'action' => 'survey-process')));
        $this->view->surveyTitle = $surveys->current()->title;
        // populate the form, if we were bounced from a save fail.
        $this->view->surveyForm = $surveys->getSurveyForm(1);
    }

    public function surveyProcessAction() {
        $userId = 1; // to be gotten from auth.
        $surveys = new DEC_Survey($userId, $this->view->url(array('controller' => 'survey', 'action' => 'survey-process')));
        
        // get id from form posting.
        $result = $surveys->saveSurveyForm($this->_request->getParam('s_id', 0), $this->_request->getPost());
        
        var_dump($result);
        // if result is true, go to thanks
        // if result is false, go to original page.
        // save post values, send off
    }
}


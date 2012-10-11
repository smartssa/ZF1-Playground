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
        $this->view->surveyForm = $surveys->getSurveyForm($this->_request->getParam('s_id', 1));
        $this->view->surveys = $surveys;
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
    
    public function statsAction() {
        $surveys = new DEC_Survey();
        $this->view->surveys = $surveys;
        $id = $this->_request->getParam('s_id', 0);
        
        if ($id > 0) {
            // we've got one!
            
            // show questions, answers and stats.
        }
    }
    
    public function managerAction() {
        // default: list surveys
        // if id is present, list questions
        // if question is selected, list answers
        $surveys = new DEC_Survey();
        $this->view->surveys = $surveys;
        
        $id = $this->_request->getParam('s_id', 0);
        
        if ($id > 0) {
            // we've got one!
            
            if ($q_id > 0) {
                // expanded question too!
            }
            
            // show an add a question form
        }
    }
    
    public function saveAction() {
    
    }
    
    public function disableAction() {
        
    }
}

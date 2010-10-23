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
        // action body
        $english = array('pants' => 'no pants');
        $german  = array('pants' => 'Hosen');
        $french  = array('pants' => 'pantelons');

        $options = array('adapter' => 'array',
                'content' => '../application/translation/en.php',
                'locale'  => 'en');
        
        $translate = new Zend_Translate($options);
        
        $translate->addTranslation('../application/translation/de.php', 'de');
        $translate->addTranslation('../application/translation/fr.php', 'fr');
        
        $translate->setLocale(Zend_Registry::get('Zend_Locale'));

        Zend_Registry::set('Zend_Translate', $translate);

        Zend_Debug::dump(Zend_Registry::get('Zend_Locale'));
        Zend_Debug::dump(Zend_Registry::get('Zend_Locale')->getQuestion());
        Zend_Debug::dump($translate);

        $this->view->date = new Zend_Date();

    }

    /**
     * Enter description here ...
     */
    public function runTestsAction()
    {
        $return = shell_exec(APPLICATION_PATH . '/../tests/run-tests.sh');
        $this->view->testResults = $return;
    }


}


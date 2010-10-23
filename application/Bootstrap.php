<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initConfig()
    {
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
    }

    protected function _initLocale(){
        // default fallback is in application.ini
        $locale = new Zend_Locale('auto');
        Zend_Registry::set('Zend_Locale', $locale);
    }

    protected function _initRegisterLogger() {
        $this->bootstrap('Log');
        $logger = $this->getResource('Log');
        Zend_Registry::set('Zend_Log', $logger);
    }
    
    protected function _initRoutes() {
        // setup routes here.
    }

}
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
        $this->bootstrap('frontcontroller');
        $front  = $this->getResource('frontcontroller');
        $router = $front->getRouter();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
        $router->addConfig($config->routes);
    }

    protected function _initNavigation() {
        $conf = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $nav  = new Zend_Navigation();
        $nav->addPages($conf);
        Zend_registry::set('Zend_Navigation', $nav);
    }
}
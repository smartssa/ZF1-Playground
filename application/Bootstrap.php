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

    public function _initErrorHandler()
    {
        // make sure the frontcontroller has been setup
        $this->bootstrap('frontcontroller');
        $frontController = $this->getResource('frontcontroller');
        // option from the config file
        $pluginOptions   = $this->getOption('errorhandler');
        $className       = $pluginOptions['class'];

        // I'm using zend_loader::loadClass() so it will throw exception if the class is invalid.
        try {
            Zend_Loader::loadClass($className);
            $plugin          = new $className($pluginOptions['options']);
            $frontController->registerPlugin($plugin);
            return $plugin;
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

}
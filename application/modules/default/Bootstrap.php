<?php
class Default_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected $_moduleName = 'default';

    protected function _initConfiguration()
    {
        $options = $this->getApplication()->getOptions();
         
        set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/models'),
        get_include_path(),
        )));

        return $options;
        
    }
}

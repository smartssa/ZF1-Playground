<?php
/**
 * @version $Id$
 */
// Initialize the application path and autoloading
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(APPLICATION_PATH . '/library', get_include_path(),)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace('TR_');
Zend_Loader_Autoloader::getInstance()->registerNamespace('DEC_');
Zend_Loader_Autoloader::getInstance()->registerNamespace('Phirehose_');

// command line options
$getopt = new Zend_Console_Getopt(array(
          'env|e=s'    => 'Environment override, default is "development"',
          'help|h'     => 'Help -- this awesome usage message'));

// Initialize values based on presence or absence of CLI options
$env = $getopt->getOption('e');

defined('APPLICATION_ENV') || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

// zend config object
$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
Zend_Registry::set('config', $config);

// database
if (isset($config->resources->multidb)) {
	$dbAdapter = Zend_Db::factory($config->resources->multidb->readonly->adapter,
	$config->resources->multidb->readonly->toArray());
} else {
	$dbAdapter = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params->toArray());
}

Zend_Db_Table::setDefaultAdapter($dbAdapter);

// .. and so she begins...
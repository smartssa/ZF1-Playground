<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

#
$frontendOptions = array(
   'lifetime' => 7200,
   'regexps' => array(
       // cache the whole IndexController
       '^/$' => array('cache' => true),
       // cache the whole IndexController
       '^/index/' => array('cache' => true),
       // we don't cache the ArticleController...
       '^/article/' => array('cache' => false),
       // ... but we cache the "view" action of this ArticleController
       '^/article/view/' => array(
           'cache' => true,
           // and we cache even there are some variables in $_POST
           'cache_with_post_variables' => true,
           // but the cache will be dependent on the $_POST array
           'make_id_with_post_variables' => true
       ))
);
$backendOptions  = array();
$cache = Zend_Cache::factory('Page',
                             'File',
                             $frontendOptions,
                             $backendOptions);
//$cache->start();

date_default_timezone_set('EST');

$application->bootstrap()
            ->run();

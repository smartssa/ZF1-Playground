<?php

class TestController extends PG_Controller_Action
{

    public function init()
    {
         parent::init();
    }

    public function indexAction()
    {
        //
    }

    public function speedAction()
    {

        $results = array();

        $i = 0;
        // do real time tests.
        $results[$i] = array('description' => 'Pre Tests',
                           'time' => '--',
                            'memorys' => round((memory_get_usage()/1024/1024), 2),
                            'memoryf' => '--');
        $i++;
        $results[$i]['description'] = "Load GB and FetchAll";
        $start = microtime(true);
        $results[$i]['memorys'] = round((memory_get_usage()/1024/1024), 2);
        $gb = new Application_Model_GuestbookMapper();
        $gb->fetchAll();
        $end = microtime(true);
        $results[$i]['time'] = round($end - $start, 8);
        $results[$i]['memoryf'] = round((memory_get_usage()/1024/1024), 2);
        unset($gb);

        $i++;
        $results[$i] = array('description' => 'End Tests',
                           'time' => '--',
                            'memorys' => '--',
                            'memoryf' => round((memory_get_usage()/1024/1024), 2));


        $this->view->results = $results;
    }
    
    public function somethingElseAction() 
    {
        // view only.
    }
}


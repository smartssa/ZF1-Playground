<?php

class AjaxController extends PG_Controller_Action {

    public function init() {

        parent::init();

    }

    public function getNavAction() {
        // return the nav branch for the requested string.
    }
    
    public function getImageListAction() {
    	// weeeee
    	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$last_id = $this->_request->getParam('last_id', 0);
    	$limit   = $this->_request->getParam('limit', 10);

    	$sql = 'SELECT * FROM image_stream WHERE enabled = 1 AND id > ? ORDER BY RAND() LIMIT ' . $limit . '';
    	
    	$result = $db->query($sql, array($last_id));
    	
    	$rowset = $result->fetchAll();
    	
    	header('content-type: application/json');
    	echo json_encode($rowset);
    	exit();
    }
}
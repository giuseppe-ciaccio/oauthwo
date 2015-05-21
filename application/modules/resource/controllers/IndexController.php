<?php

// For some strange reason, these seems to be needed...don't delete

require_once(realpath(APPLICATION_PATH . '/modules/resource/models/Mapper/Abstract.php'));
require_once(realpath(APPLICATION_PATH . '/modules/resource/models/Mapper/RSetScopes.php'));
require_once(realpath(APPLICATION_PATH . '/modules/resource/models/Mapper/RSetTable.php'));


class Resource_IndexController extends Zend_Controller_Action
{

public function init() { }


public function registrationAction() {

	$postdata = get_object_vars(json_decode(file_get_contents("php://input")));

	$name = $postdata['name'];
	$scopes = $postdata['defscope'];

	$description = $postdata['description'];
	$setid = $postdata['id'];
	$type = $postdata['type'];

	$endpoint = $postdata['endpoint'];

	$rset_info = new Resource_Model_Mapper_RSetTable();
	$rset_nuovo = new Resource_Model_RSet($setid, $name, $description, $type, $endpoint, $scopes);
	$rset_info->save($rset_nuovo);

	$exp_scopes = explode(",",$scopes);

	foreach($exp_scopes as $sc){
		$srs = new Resource_Model_Mapper_RSetScopes();
		$rset_scopes_nuovo = new Resource_Model_RSetScopes($sc, $setid);
		$srs->save($rset_scopes_nuovo);
	}
}


public function searchAction(){
	$form = new Resource_Form_SearchForm();
	$this->view->form = $form;
}


public function searchresultAction(){
	$request = $this->getRequest();
	if ($request->isPost()) {
		$values = $request->getPost();
	}
	$res = new Resource_Model_Mapper_RSetTable();
	// Last element contains the label of the clicked button of the form,
	// despite the form button had the attribute "ignore".
	// Trim it away.
	array_pop($values);
//$log = Zend_Registry::get('log');
//$log->log("query: ".implode(';',$values),0);
	$results = $res->search($values);
	$this->view->resu = $results;
}

}

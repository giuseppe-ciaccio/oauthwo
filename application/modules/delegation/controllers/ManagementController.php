<?php

class Delegation_ManagementController extends Zend_Controller_Action {

/**
 * Scope delimiter
 * @var string
 */
    protected $_DELIMITER = ' ';
	
/**
 * Delegation mapper
 * @var Delegation_Mapper_Delegation
 */
    protected $delMapper;
	
/**
 * The logged user's id
 * @var string
 */
    protected $loggedUser;
	
	
    public function init(){
	$this->delMapper = new Delegation_Mapper_Delegation();
	$this->loggedUser = Zend_Auth::getInstance()->getIdentity();
    }


    public function indexAction() {
   	$form = $this->getForm();
	$param = $this->_getParam("wrong");
	if ($param == NULL)
		$form->setDescription('Delegate someone else to authorize access to your personal data');
	else if ($param == 'inventr')
		$form->setDescription('Invalid entries');
	else
		$form->setDescription('Unknown error');
	$this->view->form = $form;
	$this->view->delegationList = $this->getDelegationList();
	$this->view->pendingDelegationsSent = $this->getPendingDelegationsSent();
	$this->view->pendingDelegationsReceived = $this->getPendingDelegationsReceived();
	$this->view->receivedDelegations = $this->getReceivedDelegations();
	$s = new Zend_Session_Namespace('delegation');
	if(isset($s->role))
		$this->view->loggedRole = true;
    }


    public function processAction() {
	$request = $this->getRequest();
    	
    	// Check if we have a POST request
    	if (!$request->isPost())
    		return $this->_helper->redirector('index');
    	
    	$post = $request->getPost();
    	$form = $this->getForm();
    	
    	if (!$form->isValid($post))
		// Invalid entries
		$this->_helper->redirector(
			'index','management','delegation',
			array('wrong'=>'inventr'));
    	
    	$selectedDelegate = $post['selectDelegate'];
    	$selectedScopes = $post['selectScopes'];
    	$hours = $post['hours'];
    	$days = $post['days'];
    	// default duration of a delegation: 1 hour
	if (!isset($post['hours']) || $hours == '')
		if (!isset($post['days']) || $days == '')
			$hours = 1;
		else
			$hours = 0;
	if (!isset($post['days']) || $days == '') $days = 0;
    	
    	$date = new Zend_Date();
    	$date->add($days, Zend_Date::DAY_SHORT);
    	$date->add($hours, Zend_Date::HOUR);
    	
    	$delegation = new Delegation_Model_Delegation();
    	$delegation->setDelegator($this->loggedUser)
    			   ->setDelegate($selectedDelegate)
    			   ->setExpDate($date->toString("yyyy-MM-dd HH:mm:ss"))
    			   ->setState(0)
    			   ->setCode(mt_rand());
    			 
    	if(!empty($selectedScopes))
    		$delegation->setScopes(implode($this->_DELIMITER, $selectedScopes));
    	
    	//Add the pending delegation
    	$this->delMapper->addDelegation($delegation);
    	
    	//Send a notification via mail to the delegate. The delegate confirms
    	//by clicking on the accept link in the notification email.
//TODO: schifezza
    	$url = $this->view->serverUrl('/oauth/delegation/management/accept');
    	try {
    		$this->delMapper->delegationCreationMail($this->loggedUser, $selectedDelegate, $selectedScopes, $delegation, $url);
    	} catch (Exception $e){}
    		
    	return $this->_helper->redirector('index');
    }


    public function acceptAction(){
	$code = $this->_request->getParam('code');
	$e = $this->delMapper->acceptDelegation($code);
	if (!$e)
		$this->view->msg = "Delegation not found (maybe revoked or already accepted)";
	else
		return $this->_helper->redirector('index');
    }


    public function processeditAction() {
	$request = $this->getRequest();
	// Check if we have a POST request
	if (!$request->isPost())
		return $this->_helper->redirector('index');
	$code = $this->_request->getParam('code');
	$delegator = $this->_request->getParam('delegator');
	$delegate = $this->_request->getParam('delegate');
	if($delegator != $this->loggedUser)
		return $this->_helper->redirector('index');
	$post = $request->getPost();
	$selectedScopes = $post['selectScopes'];
		 
	$delegation = new Delegation_Model_Delegation();
	$delegation->setCode($code)
			->setDelegator($delegator)
			->setDelegate($delegate);
	if(!empty($selectedScopes))
		$delegation->setScopes(
			implode($this->_DELIMITER, $selectedScopes));
	else
		return $this->_helper->redirector('index');

	$this->delMapper->editDelegation($delegation);

//TODO: schifezza
    	$url = $this->view->serverUrl('/oauth/delegation/management/accept');
	try {
    		$this->delMapper->delegationCreationMail($delegator, $delegate, $selectedScopes, $delegation, $url);
	} catch(Exception $e){}
		
	return $this->_helper->redirector('index');

    }


    public function deleteAction() {
	$request = $this->getRequest();
	if (!$request->isGet())
		return $this->_helper->redirector('index');
	$code = $this->_request->getParam('code');
	$res = $this->delMapper->findDelegatorAndDelegate($code);
	$delegator = $res['delegator'];
	$delegate = $res['delegate'];
	if($delegator == $this->loggedUser){
		$this->delMapper->revokeDelegation($code);
		$this->delMapper->delegationDeletionMail($delegator, $delegate);
	}
	else if($delegate == $this->loggedUser){
		$this->delMapper->revokeDelegation($code);
		$this->delMapper->delegationDeletionMail($delegate, $delegator);
	}
	$this->_helper->redirector('index');
    }


    public function editAction() {
	$request = $this->getRequest();
	if (!$request->isGet())
		return $this->_helper->redirector('index');
	$code = $this->_request->getParam('code');
	$res = $this->delMapper->findDelegatorAndDelegate($code);
	$delegator = $res['delegator'];
	$delegate = $res['delegate'];
	if($delegator == $this->loggedUser)
		$this->view->form =
			$this->getEditForm($code, $delegator, $delegate);
	else
		$this->_helper->redirector('index');
    }


    public function revokeAction() {
	$request = $this->getRequest();
	if (!$request->isGet())
		return $this->_helper->redirector('index');
	$code = $this->_request->getParam('code');
	$res = $this->delMapper->findDelegatorAndDelegate($code);
	$delegator = $res['delegator'];
	$delegate = $res['delegate'];
//$log = Zend_Registry::get('log');
//$log->log("Delegation: ".$code." delegator ".$res['delegator']." delegate ".$res['delegate'],0);
	if($delegator == $this->loggedUser || $delegate == $this->loggedUser)
		$this->delMapper->revokeDelegation($code);
	$this->_helper->redirector('index');
    }


    protected function getForm() {
	$action = $this->view->url(array('module' => 'delegation',
			'controller' => 'management',
			'action'     => 'process'), 'default');

	$form = new Delegation_Form_ManagementForm(array(
			'action' => $action,
			'method' => 'post',
	));

	$allUsers = $this->delMapper->findAllUsers($this->loggedUser);
	$allScopes = $this->delMapper->findAllScopes();
	$form->buildForm($allUsers, $allScopes);
	return $form;
    }

 
    protected function getEditForm($code, $delegator, $delegate) {
	$params = '?code='.$code.'&delegator='.$delegator.'&delegate='.$delegate;
	$form = new Delegation_Form_ManagementEditForm(array(
			'action' => 'processedit'.$params,
			'method' => 'post',
	));

	$allUsers = $this->delMapper->findAllUsers($this->loggedUser);
	$allScopes = $this->delMapper->findAllScopes();
	$defaultScopes = $this->delMapper->findScopes($delegator, $delegate);
	$form->buildForm($allUsers, $allScopes, $delegator, $delegate, $defaultScopes);
	return $form;
    }


    protected function getDelegationList() {
	$delegations = $this->delMapper->findDelegationsOfDelegator($this->loggedUser);
	return $delegations;
    }


    protected function getPendingDelegationsSent() {
	$pending = $this->delMapper->findPendingDelegationsSent($this->loggedUser);
	return $pending;
    }


    protected function getPendingDelegationsReceived() {
	$pending = $this->delMapper->findPendingDelegationsReceived($this->loggedUser);
	return $pending;
    }


    protected function getReceivedDelegations() {
	$received = $this->delMapper->findDelegationsOfDelegate($this->loggedUser);
	return $received;
    }
	
/**
 * Ensures the user is logged in with Zend_Auth, else prompt the login
 *
 */
    public function preDispatch() {
	if (!Zend_Auth::getInstance()->hasIdentity()) {
		$requestUri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
		$sessionLast = new Zend_Session_Namespace('lastRequest');
		$sessionLast->lastRequestUri = $requestUri;

		$this->_helper->redirector('index', 'index', 'login');
	}
    }

}

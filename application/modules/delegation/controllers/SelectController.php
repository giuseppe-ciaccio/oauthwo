<?php

class Delegation_SelectController extends Zend_Controller_Action {
		
	/**
	 * Delegation mapper
	 * @var Delegation_Mapper_Delegation
	 */
	protected $delMapper;
	
	/**
	 * Splitted Uri
	 * @var array of string (keys: 'base', 'scopes', 'state')
	 */
	protected $Uri;
	
	/**
	 * Array degli scope dell'uri
	 * @var array of string
	 */
	protected $scopeArray;
	
	/**
	 * The delegators of the user
	 * @var array of string
	 */
	protected $delegators;
	
	/**
	 * LastRequest session namespace
	 * @var session namespace
	 */
	protected $session_lastRequest;
	
	/**
	 * Delegation session namespace
	 * @var session namespace
	 */
	protected $session_delegation;
	
	/**
	 * Scope delimiter
	 * @var string
	 */
	protected $_DELIMITER = ' ';
	
    public function init() {
    	
    	$this->delMapper = new Delegation_Mapper_Delegation();
    	
    	$this->session_lastRequest = new Zend_Session_Namespace('lastRequest');
    	$this->session_delegation = new Zend_Session_Namespace('delegation');
    	$this->Uri = $this->splitUri($this->session_lastRequest->lastRequestUri);
    	$this->scopeArray = $this->toScopeArray($this->Uri['scopes']);
    	
    }


    public function indexAction() {
    	
        //Se l'utente e' loggato con un ruolo, mostra il form dei ruoli
	//Altrimenti il form delle deleghe

	if(isset($this->session_delegation->role))
		$this->view->form = $this->getForm4Roles();
	else
		$this->view->form = $this->getForm();

    }


    public function processAction() {

    	$request = $this->getRequest();
    	
    	// Check if we have a POST request
    	if (!$request->isPost())
    		return $this->_helper->redirector('index');
    	
    	$post = $request->getPost();
    	$form = $this->getForm();
    	
    	if (!$form->isValid($post))
		$this->_helper->redirector('index','select','delegation');
    	
    	$selectedUser = $post['selectUser'];
	if (!isset($selectedUser) || trim($selectedUser)==='')
		$this->_helper->redirector('index','select','delegation');

    	$loggedUser = Zend_Auth::getInstance()->getIdentity();
    	
    	//if the delegator is the logged user: normal flow
    	if($selectedUser == $loggedUser){
    		$this->session_delegation->usesDelegation = false;
    		$this->_helper->redirector->goToUrl($this->view->serverUrl($this->session_lastRequest->lastRequestUri));
    	}
    	
    	//otherwise, use the delegation flow
    	$this->session_delegation->usesDelegation = true;	
    	$delegationScopes = $this->delMapper->findScopes($selectedUser, $loggedUser);
    	$intersectedScopes = array_intersect($this->scopeArray, $delegationScopes);
    	$intersectedScopeStr = implode('+', $intersectedScopes);
		
    	//builds the new URI
    	$this->session_delegation->delegator = $selectedUser;
    	$newUri = $this->buildUri($this->Uri['base'], $intersectedScopeStr, $this->Uri['state']);
    	$this->_helper->redirector->goToUrl($this->view->serverUrl($newUri));

    }


    public function process4rolesAction() {

    	$request = $this->getRequest();
    	 
    	// Check if we have a POST request
    	if (!$request->isPost())
    		return $this->_helper->redirector('index');
    	 
    	$post = $request->getPost();
    	$form = $this->getForm4Roles();
    	 
    	if (!$form->isValid($post))
		$this->_helper->redirector('index','select','delegation');
    	 
    	$selectedUser = $post['selectUser'];
	if (!isset($selectedUser) || trim($selectedUser)==='')
		$this->_helper->redirector('index','select','delegation');
    	
    	$this->session_delegation->usesDelegation = true;
    	
    	$role = $this->delMapper->findRole($this->session_delegation->role);
    	$roleScopes = explode($this->_DELIMITER, $role->getRoleScopes());
    	$intersectedScopes = array_intersect($this->scopeArray, $roleScopes);
    	$intersectedScopeStr = implode('+', $intersectedScopes);
    	
    	//builds the new URI
    	$this->session_delegation->delegator = $selectedUser;
    	$newUri = $this->buildUri($this->Uri['base'], $intersectedScopeStr, $this->Uri['state']);
    	$this->_helper->redirector->goToUrl($this->view->serverUrl($newUri));

    }


    protected function getForm() {
    	
    	$action = $this->view->url(array('module' => 'delegation',
    			'controller' => 'select',
    			'action'     => 'process'), 'default');
    	
    	$form = new Delegation_Form_DelegationForm(array(
    			'action' => $action,
    			'method' => 'post',
    	));
    		
    	$loggedUser = Zend_Auth::getInstance()->getIdentity();
    	$users = array();
    	
    	//Setta i deleganti
    	$this->delegators = $this->delMapper->findDelegators($loggedUser, $this->scopeArray);
    	$users = $this->delegators;
    	array_unshift($users, $loggedUser);
    	//Popola il form con i deleganti
    	$form->buildForm($users);
    	return $form;

    }


    protected function getForm4Roles(){

    	$action = $this->view->url(array('module' => 'delegation',
    			'controller' => 'select',
    			'action'     => 'process4roles'), 'default');
    	
    	$form = new Delegation_Form_DelegationForm4Roles(array(
    			'action' => $action,
    			'method' => 'post',
    	));
    	 
    	$users = array();
    	
    	$roleId = $this->session_delegation->role;
    	$role = $this->delMapper->findRole($roleId);
    	
    	//Se almeno uno scope del ruolo e' incluso negli scope dell'url
    	//allora il ruolo e' attivo e si puo' usare per accedere ai dati
    	//di tutti gli utenti
    	if($this->delMapper->canRoleSeeUsers($role, $this->scopeArray)){
    		$users = $this->delMapper->findAllUsers();
    		$form->buildForm($users);
    	}
    	else $form = "Role can't use any specified scope";
    	return $form;

    }


    private function splitUri($uri){
    	$parts = explode("&scope=", $uri);
    	if(count($parts)<2) return array('base' => $parts[0],
    					 'scopes' => '',
    					 'state' => '');
    	$parts2 = explode("&state=", $parts[1]);
    	return array('base' => $parts[0], 
    				 'scopes' => $parts2[0],
    				 'state' => $parts2[1]);
    }
    
    private function toScopeArray($scopeStr){
    	return explode("+", $scopeStr);
    }
    
    private function buildUri($base, $scopes, $state){
    	return $base . "&scope=" . $scopes . "&state=" . $state;
    }
}

<?php

class Delegation_RoleController extends Zend_Controller_Action {
		
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
	 * The role of the user
	 * @var array of Delegation_Model_Role
	 */
	protected $role;
	
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
    
    
    public function indexAction(){
    	$this->view->form = $this->getRoleChoiceForm();
    }


    public function authAction(){
    	
    	$request = $this->getRequest();
    	 
    	// Check if we have a POST request
    	if (!$request->isPost())
		$this->_helper->redirector('index');
    	 
    	$post = $request->getPost();
    	$form = $this->getRoleChoiceForm();
    	 
    	if (!$form->isValid($post))
		$this->_helper->redirector('index');
    	 
    	$selectedRole = $post['selectRole'];
	$this->session_delegation->selectedRole = $selectedRole;
    	
    	//Ask the role server if the role is legit
    	$role = $this->delMapper->findRole($selectedRole);
//TODO: schifezza
    	$url = $this->view->serverUrl($this->view->baseUrl()).'/delegation/role/verify';
    	$this->_helper->redirector->gotoUrl($role->getRoleUri() . '?role=' .$selectedRole.'&url='.$url);
    	
    }


    /**
     * Verify SAML assertion of role
     *
     */

    public function verifyAction() {
    	
    	require_once realpath(APPLICATION_PATH . '/../library/php-saml/_toolkit_loader.php');
    	
    	$post = $this->_request->getPost();
    	$assertion = base64_decode($post['assertion']);  
    	$saml = new DOMDocument();
    	$saml->loadXML($assertion);
    	
    	$samlresponse = new OneLogin_Saml2_Response(
		new OneLogin_Saml2_Settings(),
		$post['assertion']);
    	if(!$samlresponse->isValid())
   		throw new Exception("Could not validate SAML Response");
    	
    	$xpath = new DOMXPath($saml);
    	$xpath->registerNamespace(
		"saml","urn:oasis:names:tc:SAML:2.0:assertion");
    	$role = (int)$xpath->query("//saml:Attribute[@Name = 'role_id']/saml:AttributeValue")->item(0)->nodeValue;
    	$id = trim($xpath->query("//saml:Attribute[@Name = 'subject_id']/saml:AttributeValue")->item(0)->nodeValue);   	

    	if($this->session_delegation->selectedRole == $role)  	
    		$this->session_delegation->role = $role;
    	else 
    		throw new Exception("Role mismatch");

    	unset($this->session_delegation->selectedRole);
    	
    	//Create a Zend_Auth using role and id
    	$roleName = $this->delMapper->findRole($role)->getRoleName();
    	$storage = Zend_Auth::getInstance()->getStorage();
    	$storage->write($roleName . ' ' . $id); 
    	
    	$s = new Zend_Session_Namespace('lastRequest');
    	$this->_helper->redirector->gotoUrl($this->view->serverUrl($s->lastRequestUri));

    }


    protected function getRoleChoiceForm(){
    	
    	$action = $this->view->url(array('module' => 'delegation',
    			'controller' => 'role',
    			'action'     => 'auth'), 'default');
    	
    	$form = new Delegation_Form_RoleForm(array(
    			'action' => $action,
    			'method' => 'post',
    	));
    	
    	$roles = $this->delMapper->findAllRoles();
    	$form->buildForm($roles);
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


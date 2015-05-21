<?php

class Login_IndexController extends Zend_Controller_Action {

    public function init() {

    }


    protected function getAuthAdapter(array $params) {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('user')
            ->setIdentityColumn('user_id')
            ->setCredentialColumn('user_password')
            ->setCredentialTreatment('MD5(?)');

        $authAdapter->setIdentity($params['username']);
        $authAdapter->setCredential($params['password']);

        return $authAdapter;
    }


    protected function afterSuccessfulLogin() {
        $session = new Zend_Session_Namespace('lastRequest');
        if (isset($session->lastRequestUri)) {
            $redirect_uri=$session->lastRequestUri;
            unset($session->lastRequestUri);
            $this->_helper->redirector->gotoUrl($this->view->serverUrl($redirect_uri));
            return;
        }
        $this->_helper->redirector('index', 'index', 'default');
    }


    public function indexAction() {
        $form = $this->getForm();
        $param = $this->_getParam("wrong");
        if ($param == NULL)
            $form->setDescription('Sign in the authorization service');
        else {
            if ($param == 'inventr')
                $form->setDescription('Invalid entries. Try again.');
            else if ($param == 'invcred')
                $form->setDescription('Invalid credentials. Try again.');
            else
                $form->setDescription('Unknown error. Try again.');
        }
        $this->view->form = $form;
    }


    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();

        //reset role (delegation module)
        $d = new Zend_Session_Namespace('delegation');
        unset($d->role);

        $this->_helper->redirector('index'); // back to login page
    }


    public function processAction() {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost())
            return $this->_helper->redirector('index');

        $post = $request->getPost();
        $form = $this->getForm();

        // Login by role?
        if ($post['role'])
	    $this->_helper->redirector('index','role','delegation');

        // validate the form
        if (!$form->isValid($post)) {
            // Invalid entries
//	    $this->_helper->redirector->gotoUrlAndExit('login?wrong=inventr');
	    $this->_helper->redirector(
                    'index','index','login',array('wrong'=>'inventr'));
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            // Invalid credentials
	    $this->_helper->redirector(
                    'index','index','login',array('wrong'=>'invcred'));
        }

        // We're authenticated!
        $this->afterSuccessfulLogin();
    }


    public function preDispatch() {

        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName())
	        $this->_helper->redirector('index', 'index', 'default');
        } else {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }


    protected function getForm() {
        $formAction = $this->view->url(array(
			'module' => 'login',
			'controller' => 'index',
			'action'     => 'process'), 'default');
        $form = new Login_Form_LoginForm(array(
                    'action' => $formAction,
                    'method' => 'post',
                ));
        $form->injectRequestValues($this->getRequest()->getParams());
        return $form;
    }

}

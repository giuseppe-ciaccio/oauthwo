<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

        /**
         * Ensures the user is logged in with Zend_Auth, else prompt the login
         *
         */
        public function preDispatch() {
                if (!Zend_Auth::getInstance()->hasIdentity()) {
                        $urlOptions = array(
                                'module'=>'login',
                                'controller'=>'index',
                                'action'=>'index');
                        $this->_helper->redirector->gotoRoute($urlOptions);
                }
        }

}

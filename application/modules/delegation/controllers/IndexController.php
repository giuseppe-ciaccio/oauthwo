<?php

class Delegation_IndexController extends Zend_Controller_Action {
		
    public function init() {
        $this->_helper->redirector('index','management','delegation');
    }

}

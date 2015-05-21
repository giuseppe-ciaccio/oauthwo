<?php
class Delegation_Form_DelegationForm extends Zend_Form {
		
protected $users;
	
public function init(){
	//decorators
	$this->setDecorators(array(
		'FormElements',
		array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
		array('Description', array('placement' => 'prepend')),
		'Form'
	));
}

public function buildForm($users){
	$usersArray = array();
	foreach($users as $u){
		$usersArray[$u] = $u;
	}
	$select = $this->addElement('select', 'selectUser', array(
		'required' => true,
		'ignore' =>false,
		'label' => 'Available users',
		'description' => 'Select the user for whom you are going to give authorization',
		'multiOptions' => $usersArray,
		'validators' => array('EmailAddress','NotEmpty')
// NotEmpty seems not working
// TODO perfezionare i validator e aggiungere messaggi:
// https://stackoverflow.com/questions/28646730/zend-form-validation-notempty-stringlength
	));
	$process = $this->addElement('submit', 'process', array(
     		'required' => false,
     		'ignore' => true,
     		'label' => 'Proceed',
	));
}

}

<?php
class Delegation_Form_ManagementForm extends Zend_Form {
	
	public function init(){

		//decorators
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
			array('Description', array('placement' => 'prepend')),
			'Form'
		));
	}
	
	public function buildForm($users, $scopes){
		
		$acElem = new ZendX_JQuery_Form_Element_AutoComplete('selectDelegate');
		$acElem->setLabel('Delegate');
		$acElem->setDescription('Select the user you want to delegate');
		$acElem->setJQueryParam('data', $users);
		$this->addElement($acElem);
				
		$this->addElement('multiCheckbox', 'selectScopes', array(
			'required' => true,
			'ignore' =>false,
			'label' => 'Scopes',
			'description' => 'Select the scopes of the delegation',
			'multiOptions' => $scopes
		));

		$this->addElement('text', 'days', array(
			'required' => false,
			'validators' => array('Digits'),
			'label' => 'Duration (days)',
		));

		$this->addElement('text', 'hours', array(
			'required' => false,
			'validators' => array('Digits'),
			'label' => 'Duration (hours)',
		));

		$this->addElement('submit', 'add', array(
			'required' => true,
			'ignore' => true,
			'label' => 'Add',
		));

		$this->setDefaults(array(
		    'days' => 0,
		    'hours' => 1,
		));
	}
}

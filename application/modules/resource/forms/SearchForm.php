<?php

class Resource_Form_SearchForm extends Zend_Form {
 
public function init()
{
	// initialize form
	$this->setAction('searchresult')
		->setMethod('post')
		->setDescription('insert one or more keywords, use comma as separator');

	// create text input
	$name = new Zend_Form_Element_Text('search');
	$name->setLabel('Search Keywords:')
		 ->setOptions(array('id' => 'search_name'))
		 ->addValidator('NotEmpty', false)
		 ->addFilter('HtmlEntities');

	$this->addElement($name);

	// add submit button
	$this->addElement('submit', 'submit', array(
		'label' => 'Search',
		'ignore' => true
	));

	// decorator, for attaching descriptions or hints to the form
	$this->setDecorators(array(
		'FormElements',
		array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
		array('Description', array('placement' => 'prepend')),
		'Form'
	));

}
}

<?php
class Resourceset_Form_Search extends Zend_Form
{
	  public function init()
	  {
		// initialize form
		$this->setAction('searchresult')
			  ->setMethod('post');
		// create text input for name

		$name = new Zend_Form_Element_Text('search');
		$name->setLabel('Search Keywords:')
			 ->setOptions(array('id' => 'search_name'))
			 ->addValidator('NotEmpty', false)
			 ->addFilter('HTMLEntities');

		// create submit button
		 $submit = $this->createElement('submit', 'submit', array(
		 'label' => 'Search',
		 'class' => 'submit'
		));

		// attach elements to form
		$this->addElement($name)
			 ->addElement($submit);
	  }
	}


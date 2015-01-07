<?php

class Resourceset_Model_DbTable_RSetScopes extends Zend_Db_Table_Abstract {
    protected $_name = 'rsets_scopes';
	//protected $_primary = 'scope_uri';
	protected $_primary = array('scope_uri', 'rset_id');
}
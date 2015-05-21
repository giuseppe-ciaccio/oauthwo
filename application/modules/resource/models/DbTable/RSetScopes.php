<?php

class Resource_Model_DbTable_RSetScopes extends Zend_Db_Table_Abstract {
	protected $_name = 'rset_scope';
	protected $_primary = array('scope_uri', 'rset_id');
}

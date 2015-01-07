<?php

require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'DbTable' . DIRECTORY_SEPARATOR . 'rset_table.php');
require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'DbTable' . DIRECTORY_SEPARATOR . 'rsets_scopes.php');


abstract class Resourceset_Mapper_Abstract {
    /**
     * The table used by this mapper
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;

    /**
     * The table object to be used
     *
     * @var string
     */
    protected $table_name;

	public function setDbTable($dbTable) {
		if (is_string($dbTable))
			$dbTable = new $dbTable();

		if (! $dbTable instanceof Zend_Db_Table_Abstract)
			throw new Exception('Invalid table data gateway provided');

		$this->_dbTable = $dbTable;
		return $this;
	}

	/**
	 * Returns the used db table
	 *
	 * @return Zend_Db_Table_Abstract
	 */
	public function getDbTable() {
		if (null === $this->_dbTable) {
			$this->setDbTable($this->table_name);
		}
		return $this->_dbTable;
	}
}
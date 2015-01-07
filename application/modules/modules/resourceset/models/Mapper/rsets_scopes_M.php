<?php

require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'rset_scopes_Model.php');

class Resourceset_Mapper_RSetScopes extends Resourceset_Mapper_Abstract {
	public function __construct() {
		$this->table_name = 'Resourceset_Model_DbTable_RSetScopes';
	}


	/**
		 * trova gli scope di un resource server
		 * @param string $cf_owner
		 * @return array[int]Resourceset_Model_DbTable_RSetInfo
		 */
		public function findAll() {
			$select = $this->getDbTable()->select();
			$rows = $this->getDbTable()->fetchAll($select);

			if ($rows == null)
				return array();

			$results = array();
			foreach ($rows as $r)
				$results[] = new Resourceset_Model_ResetScopes($r->scope_uri, $r->rset_id);

			return $results;
	}

	/**
	 * trova lo scope resource set
	 * @param string $scope_uri
	 * @return array[int]Resource_Model_DbTable_RSetInfo
	 */
	public function find($sid) {
		$select = $this->getDbTable()->select();
		$select->where('scope_uri = ?', $sid);
		$rows = $this->getDbTable()->fetchAll($select);

		if ($rows == null)
			return array();

		$results = array();
		foreach ($rows as $r)
			$results[] = new Resourceset_Model_ResetScopes($r->scope_uri, $r->rset_id);

		return $results;
	}


	/**
		 * trova lo scope resource set
		 * @param string $scope_uri
		 * @return array[int]Resource_Model_DbTable_RSetInfo
		 */
		public function findRset($rid) {
			$select = $this->getDbTable()->select();
			$select->where('rset_id = ?', $rid);
			$rows = $this->getDbTable()->fetchAll($select);

			if ($rows == null)
				return array();

			$results = array();
			foreach ($rows as $r)
				$results[] = new Resourceset_Model_ResetScopes($r->scope_uri, $r->rset_id);

			return $results;
	}


	/**
			 * trova lo scope resource set
			 * @param string $scope_uri
			 * @return array[int]Resource_Model_DbTable_RSetInfo
			 */
			public function findrsetscope($rid, $sid) {
				$select = $this->getDbTable()->select();
				$select->where('rset_id = ?', $rid)
					   ->where('scope_uri = ?', $sid);
				$rows = $this->getDbTable()->fetchAll($select);

				if ($rows == null)
					return array();

				$results = array();
				foreach ($rows as $r)
					$results[] = new Resourceset_Model_ResetScopes($r->scope_uri, $r->rset_id);

				return $results;
	}


	public function save(Resourceset_Model_ResetScopes $rset) {
		        $data = array(
		            'scope_uri' => $rset->getScopeUri(),
		            'rset_id' => $rset->getRsetId(),
		        );

		        $this->getDbTable()->insert($data);
		    }



		 /**
		     * Deletes a refresh token from the DB by code
		     *
		     * @param string $code
		     * @return int
		     */
		    public function delete($scope_uri) {

		        $result = $this->getDbTable()->find($scope_uri);
		        if (0 == count($result)) {
		            return;
		        }

		        $row = $result->current();
		        $row->delete();
	    }

}

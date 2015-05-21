<?php


class Resource_Model_Mapper_RSetTable extends Resource_Model_Mapper_Abstract {

	public function __construct() {
		$this->table_name = 'Resource_Model_DbTable_RSet';
	}


	public function findAll() {
		$select = $this->getDbTable()->select();
		$rows = $this->getDbTable()->fetchAll($select);

		if ($rows == null)
			return array();

		$results = array();
		foreach ($rows as $r)
			$results[] = new Resource_Model_RSet($r->rset_id, $r->rset_name,$r->rset_description, $r->rset_type, $r->rset_uri, $r->rset_scopesuri);

		return $results;
	}


	public function find($rset_id) {
		$select = $this->getDbTable()->select();
		$select->where('rset_id = ?', $rset_id);
		$rows = $this->getDbTable()->fetchAll($select);

		if ($rows == null)
			return array();

		$results = array();
		foreach ($rows as $r)
			$results[] = new Resource_Model_RSet($r->rset_id, $r->rset_name,$r->rset_description, $r->rset_type, $r->rset_uri, $r->rset_scopesuri);

		return $results;
	}


	public function save(Resource_Model_RSet $rset) {
	        $data = array(
	            'rset_id' => $rset->getRsetId(),
	            'rset_name' => $rset->getName(),
	            'rset_description' => $rset->getDescr(),
	            'rset_type'=> $rset->getType(),
	            'rset_uri' => $rset->getUri(),
	            'rset_scopesuri' => $rset->getScopesUri(),
	        );

	        $this->getDbTable()->insert($data);
	}


	public function search($keywords) {

		$keys = implode(",",$keywords);
	    	$config = new Zend_Config_Ini(realpath(
			APPLICATION_PATH . '/configs/application.ini',
			'production'));
		$host = $config->resources->db->params->host;
		$username = $config->resources->db->params->username;
		$password = $config->resources->db->params->password;
		$dbname = $config->resources->db->params->dbname;

		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
		        'host'     => $host,
		        'username' => $username,
		        'password' => $password,
		        'dbname'   => $dbname
    		));

// TODO i nomi di tabella e campi non dovrebbero essere cablati qui
		$query = "SELECT *,MATCH(rset_name,rset_description,rset_type,rset_uri) AGAINST ('$keys' IN BOOLEAN MODE) AS relevance FROM resource_set_registration WHERE (MATCH (rset_name,rset_description,rset_type,rset_uri) AGAINST ('$keys' IN BOOLEAN MODE)) ORDER BY relevance DESC";

		$result = $db->fetchAll($query);

		if ($result != null) {
			$res = array();
			foreach ($result as $r)
    		    		$res[] = $r;
		 	  	return $res;
		} else
			return NULL;

	}


	/**
	* Deletes a refresh token from the DB by code
	*
	* @param string $code
	* @return int
	*/
	public function delete($rset_id) {
	        $result = $this->getDbTable()->find($rset_id);
	        if (0 == count($result))
	            return;

	        $row = $result->current();
	        $row->delete();
	}
}

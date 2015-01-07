<?php

require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Mapper' . DIRECTORY_SEPARATOR . 'Abstract.php');
require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'rset_model.php');

class Resourceset_Mapper_RSetTable extends Resourceset_Mapper_Abstract {

	public function __construct() {
		$this->table_name = 'Resourceset_Model_DbTable_RSet';
	}

	/**
		 * trova gli rset
		 * * @return array[int]Resourceset_Model_DbTable_RSet
		 */
		public function findAll() {
			$select = $this->getDbTable()->select();
			//$select->where('name = ?', $name);
			$rows = $this->getDbTable()->fetchAll($select);

			if ($rows == null)
				return array();

			$results = array();
			foreach ($rows as $r)
				$results[] = new Resourceset_Model_RSet($r->rset_id, $r->rset_name,$r->rset_description, $r->rset_type, $r->rset_uri, $r->rset_scopesuri);

			return $results;
		}

	/**
	 * trova un resource set di un resource server
	 * @param string $cf_owner
	 * @return array[int]Resource_Model_DbTable_RSetInfo
	 */
	public function find($rset_id) {
		$select = $this->getDbTable()->select();
		$select->where('rset_id = ?', $rset_id);
		$rows = $this->getDbTable()->fetchAll($select);

		if ($rows == null)
			return array();

		$results = array();
		foreach ($rows as $r)
			$results[] = new Resourceset_Model_RSet($r->rset_id, $r->rset_name,$r->rset_description, $r->rset_type, $r->rset_uri,$r->rset_scopesuri);

		return $results;
	}


	public function save(Resourceset_Model_RSet $rset) {
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
		    	$config = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini', 'development');
				// create https client request
				$username = $config->resources->db->params->rootuser;
				$password = $config->resources->db->params->rootpwd;
				$dbname = $config->resources->db->params->dbname;


				    $db = new Zend_Db_Adapter_Pdo_Mysql(array(
				        'host'     => 'localhost',
				        'username' => $username,
				        'password' => $password,
				        'dbname'   => $dbname
    			));


			$query = "SELECT *,MATCH(rset_name,rset_description,rset_type,rset_uri) AGAINST ('$keys' IN BOOLEAN MODE) AS relevance FROM resource_set_registration WHERE (MATCH (rset_name,rset_description,rset_type,rset_uri) AGAINST ('$keys' IN BOOLEAN MODE)) ORDER BY relevance DESC";

			$result = $db->fetchAll($query);

			if ($result == null)
				return array();
				$results = array();
				foreach ($result as $r)
    	  	  		$results[] = $r;
 		   	  	return $results;
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

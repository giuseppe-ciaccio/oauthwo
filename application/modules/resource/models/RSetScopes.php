<?php

class Resource_Model_RSetScopes {
	/**
		 * rset id
		 * @var string
		 */
		private $scope_uri;
		private $rset_id;


		public function __construct($sid, $rid) {
			$this->scope_uri = $sid;
			$this->rset_id = $rid;
		}


	public function getScopeUri(){
	   return $this->scope_uri;
	}

	public function getRsetId(){
		   return $this->rset_id;
	}

}

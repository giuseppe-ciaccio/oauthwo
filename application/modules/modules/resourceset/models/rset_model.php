<?php

class Resourceset_Model_RSet {
	/**
		 * rset id
		 * @var string
		 */
		private $rset_id;
		private $rset_name;
		private $rset_description;
		private $rset_type;
		private $rset_uri;
		private $rset_scopesuri;

		public function __construct($id, $name, $desc, $type, $uri, $scopes) {
			$this->rset_id = $id;
			$this->rset_name = $name;
			$this->rset_description = $desc;
			$this->rset_type = $type;
			$this->rset_uri = $uri;
			$this->rset_scopesuri = $scopes;
		}


	public function getRsetId(){
	   return $this->rset_id ;
	}

	public function getName(){
		   return $this->rset_name;
	}

	public function getUri(){
		   return $this->rset_uri;
	}

	public function getDescr(){
		   return $this->rset_description;
	}

	public function getType(){
			   return $this->rset_type;
	}

	public function getScopesUri(){
			   return $this->rset_scopesuri;
	}


}
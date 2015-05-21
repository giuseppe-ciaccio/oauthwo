<?php
class Delegation_Mapper_Delegation {

	/**
	 * The delimiter that divides the scopes in the db table
	 * @var string
	 */
	protected $_DELIMITER = ' ';
	
	/**
	 * The delegation table
	 * @var Delegation_Model_DbTable_Delegation
	 */
	protected $delegation_table;
	
	/**
	 * The userRole table
	 * @var Delegation_Model_DbTable_UserRole
	 */
	protected $userRole_table;
	
	/**
	 * The role table
	 * @var Delegation_Model_DbTable_Role
	 */
	protected $role_table;
	
	/**
	 * The roleScope table
	 * @var Delegation_Model_DbTable_RoleScope
	 */
	protected $roleScope_table;
	
	
	/**
	 * This object constructor
	 *
	 */
	public function __construct() {
		
		$this->delegation_table = new Delegation_Model_DbTable_Delegation();
		$this->role_table = new Delegation_Model_DbTable_Role();
		$this->roleScope_table = new Delegation_Model_DbTable_RoleScope(); 
		
		if (!$this->delegation_table instanceof Zend_Db_Table_Abstract or 
			!$this->role_table instanceof Zend_Db_Table_Abstract or
			!$this->roleScope_table instanceof Zend_Db_Table_Abstract	) {
			throw new Exception('Invalid table data gateway provided');
		}
	}

	
	/**
	 * Find delegator and delegate of a delegation identified by code,
	 * supposed unique.
	 * @param string $code
	 * @return array
	 */
	public function findDelegatorAndDelegate($code){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('code = ?', $code);
		$rows = $table->fetchAll($select);
		$res = array( 'delegator' => '', 'delegate' => '' );
		if ($rows == null) return $res;
		foreach($rows as $row) {
			$res['delegator'] = $row->delegator;
			$res['delegate'] = $row->delegate;
			return $res;
		}
	}

	/**
	 * Find the delegations of a delegator
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findDelegationsOfDelegator($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $id)
			   ->where('state = 1');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Find the delegations of a delegate
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findDelegationsOfDelegate($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegate = ?', $id)
			   ->where('state = 1');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Find the pending delegations sent by a delegator
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findPendingDelegationsSent($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $id)
			   ->where('state = 0');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Find the pending delegations received by a delegate
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findPendingDelegationsReceived($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegate = ?', $id)
			->where('state = 0');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Find all the delegations of a delegator, pending ones included
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findAllDelegationsOfDelegator($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $id);
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Find the delegators of a user having at least 1 scope in $scopes
	 * @param string $id
	 * @param array of string $scopes
	 * @return array of string
	 */
	public function findDelegators($id, $scopes=null){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegate = ?', $id);
		$select->where('expiration_date >= current_date');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			if($scopes!=null){
				$rowScopes = explode($this->_DELIMITER, $row->scopes);
				foreach($rowScopes as $rs){
					if(in_array($rs, $scopes)){
						$result[] = $row->delegator;
						continue;
					}
				}
			}
			else 
				$result[] = $row->delegator;
		}
		return $result;
	}
	
	/**
	 * Get the string of scopes from a delegation
	 * @param string $delegator
	 * @param string $delegate
	 * @return array of string
	 */
	public function findScopes($delegator, $delegate){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $delegator)
			   ->where('delegate = ?', $delegate);
		$row = $table->fetchRow($select);
		if(!$row) return array();
		return explode($this->_DELIMITER, $row->scopes);
	}
	
	
	/**
	 * Find all users except the one specified
	 * @param string $userId
	 * @return array of string
	 */
	public function findAllUsers($except=null){
		$table = new Oauth_Model_DbTable_ResourceOwner();
		$select = $table->select();
		if($except != null) $select->where('user_id <> ?', $except);
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$result[] = $row->user_id;
		}
		return $result;
	}
	
	/**
	 * Return all scopes 
	 * @return array of string (scope_id => scope_description)
	 */
	public function findAllScopes(){
		$table = new Oauth_Model_DbTable_Scope();
		$select = $table->select();
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$result[$row->scope_id] = $row->scope_description;
		}
		return $result;
	}
	
	/**
	 * Find the scopes permitted for a role
	 * @param integer $roleId
	 * @return array of string | boolean
	 */
	public function findRoleScopes($roleId){
		$table = $this->roleScope_table;
		$select = $table->select();
		$select->where('role_id = ?', $roleId);
		$row = $table->fetchRow($select);
		if(!$row) return false;
		return explode($this->_DELIMITER, $row->scopes);
	}
	
	/**
	 * Check if at least 1 scope of the role is included in the scopes specified
	 * @param array of Delegation_Model_Role $roles
	 * @param array of string $scopes
	 * @return boolean
	 */
	public function CanRoleSeeUsers($role, $scopes){
		$result = false;
		$roleScopes = explode($this->_DELIMITER, $role->getRoleScopes());
		foreach($roleScopes as $rs){
			if(in_array($rs, $scopes)){
				$result = true;
				break;
			}
		}
		return $result;
	}
	

	/**
	 * Find a role
	 * @param int $id
	 * @return boolean|Delegation_Model_Role
	 */
	public function findRole($id){
		$tableRole = $this->role_table;
		$tableRoleScope = $this->roleScope_table; 
		
		$select = $tableRole->select();
		$select->setIntegrityCheck(false)
				->from($tableRole->getName())
				->joinNatural($tableRoleScope->getName())
				->where('role_id = ?', $id);
		
		$row = $tableRole->fetchRow($select);
		if(!$row) return false;
		$role = new Delegation_Model_Role();
		$role->setRoleId($row->role_id)
		     ->setRoleName($row->role_name)
		     ->setRoleUri($row->role_uri)
		     ->setRoleScopes($row->scopes);
		return $role;
	}

	/**
	 * Find all the available roles
	 * @return array of Delegation_Model_Role
	 */
	public function findAllRoles(){
		$table = $this->role_table;
		$select = $table->select();
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$r = new Delegation_Model_Role();
			$r->setRoleId($row->role_id)
			  ->setRoleName($row->role_name)
			  ->setRoleUri($row->role_uri);
			$result[] = $r;
		}
		return $result;
	}
	
	/**
	 * Insert a delegation into the database
	 * @param Delegation_Model_Delegation $delegation
	 */
	public function addDelegation($delegation){
		
		$record = array('delegator' => $delegation->getDelegator(),
				'delegate' => $delegation->getDelegate(),
				'scopes' => $delegation->getScopes(),
				'expiration_date' => $delegation->getExpDate(),
				'state' => $delegation->getState(),
				'code' => $delegation->getCode()
		);
		$this->delegation_table->insert($record);
	}
	
	/**
	 * Accept a pending delegation
	 * @param string $code id. of delegation
	 */
	public function acceptDelegation($code){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('code = ?', $code)
			   ->where('state = 0');
		$row = $table->fetchRow($select);
		if(!$row) return false;
		
		//set the delegation as accepted (state = 1)
		$data = array( 'state' => 1 );
		$table->update($data, "code = '".$code."'");
		
		return true;
	}

	/**
	 * Edit a delegation
	 * @param Delegation_Model_Delegation $delegation
	 */
	public function editDelegation($delegation){
		if($delegation->getScopes() == "")
			throw new Exception("Scopes are empty. Chose at least 1 scope");
		$code = $delegation->getCode();
		$data = array('delegate' => $delegation->getDelegate(),
				'scopes' => $delegation->getScopes(),
				//set state as pending again
				'state' => 0,
		);
		$table = $this->delegation_table;
		$table->update($data, "code = '".$code."'");
	}
	
	/**
	 * Revoke a pending delegation (without notification)
	 * @param string $code id. of delegation
	 */
	public function revokeDelegation($code){
		$this->delegation_table->delete('code = "'.$code.'"');
	}

	/**
	 * Send an email to the address receiverMail, telling that
	 * a delegation has been made
	 * @param string $senderMail
	 * @param string $receiverMail
	 * @param array of string $scopes
	 */
	public function delegationCreationMail($senderMail, $receiverMail, $scopes, $delegation, $url){
		
		$link = $url . '?code=' . $delegation->getCode();
		
		$mail = new Zend_Mail();
		$mail->setBodyHtml("User <b>".$senderMail."</b> has delegated you for
				the following scopes: <br/><br/><b>".implode('<br/>', $scopes)."</b>.<br/><br/> To confirm
				the delegation click on the following link:<br/>
				<a href='".$link."'>Confirm delegation</a>");
		$mail->setFrom('oauth2del@gmail.com');
		//label for gmail. For tests and demo
		$exploded = explode('@', $receiverMail);
		$label = '_' . $exploded[0];
		$mail->addTo('oauth2del+'.$label.'@gmail.com', $label);
		//For a real usage: sends to the real email address
		//$mail->addTo($receiverMail);
		$mail->setSubject('[AS] New Delegation from '. $senderMail);
		$mail->send();
		
	}
	
	/**
	 * Send an email to the address receiverMail, telling that
	 * a delegation has been deleted
	 * @param string $senderMail
	 * @param string $receiverMail
	 */
	public function delegationDeletionMail($senderMail, $receiverMail){
		$mail = new Zend_Mail();
		$mail->setBodyHtml("User <b>".$senderMail."</b> has deleted his delegation");
		$mail->setFrom('oauth2del@gmail.com');
		//label for gmail. For tests and demo
		$exploded = explode('@', $receiverMail);
		$label = '_' . $exploded[0];
		$mail->addTo('oauth2del+'.$label.'@gmail.com', $label);
		//For a real usage: sends to the real email address
		//$mail->addTo($receiverMail);
		$mail->setSubject('[AS] Delegation deleted by '. $senderMail);
		$mail->send();
	}

	/**
	 * Send an email to the address receiverMail, telling that
	 * a delegation has been used
	 * @param string $senderMail
	 * @param string $receiverMail
	 */
	public function delegationUsedMail($senderMail, $receiverMail){
		$mail = new Zend_Mail();
		$mail->setBodyHtml("User <b>".$senderMail."</b> has	accessed to some of your scopes.");
		$mail->setFrom('oauth2del@gmail.com');
		//label for gmail. For tests and demo
		$exploded = explode('@', $receiverMail);
		$label = '_' . $exploded[0];
		$mail->addTo('oauth2del+'.$label.'@gmail.com', $label);
		//For a real usage: sends to the real email address
		//$mail->addTo($receiverMail);
		$mail->setSubject('[AS] Delegation used by '. $senderMail);
		$mail->send();
	}
}

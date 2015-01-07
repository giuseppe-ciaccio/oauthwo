<?php

/**
 *
 * ModelLoader.php,
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 *
 */

 require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Mapper' . DIRECTORY_SEPARATOR . 'rset_mapper.php');


/**
 *  Extends an abstract helper and is used to load models objects
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Controller_Action_Helper_ModelLoader extends Zend_Controller_Action_Helper_Abstract {

    /**
     * Loads a scope
     *
     * @param string $scope_name
     * @return Oauth_Model_Scope
     */
    public function loadScope($scope_name) {
        $scopeMapper = new Oauth_Mapper_Scope();
        $scope = $scopeMapper->find($scope_name);
        return $scope;
    }


    /**
	     * Loads a resource set
	     *
	     * @param string $rset_id
	     * @return Resourceset_Model_RSet
	     */
	    public function loadRSet($rset_id) {
	        $rsetMapper = new Resourceset_Mapper_RSetTable();
	        $rset = $rsetMapper->find($rset_id);
	        return $rset;
    }


    /**
     * Loads a client
     *
     * @param string $client_id
     * @return Oauth_Model_Client
     */
    public function loadClient($client_id) {
        $clientMapper = new Oauth_Mapper_Client();
        $client = $clientMapper->find($client_id);
        return $client;
    }

    /**
     * Loads a resource owner from the current session
     *
     * @return Oauth_Model_ResourceOwner
     */
    public function loadResourceOwnerFromSession() {
        $resourceOwnerMapper = new Oauth_Mapper_ResourceOwner();
        $resource_owner = $resourceOwnerMapper->fromSession();
        return $resource_owner;
    }

    /**
     * Loads a resource owner
     *
     * @param string $resource_owner_id
     * @return Oauth_Model_ResourceOwner
     */
    public function loadResourceOwner($resouce_owner_id) {
        $resourceOwnerMapper = new Oauth_Mapper_ResourceOwner();
        $resource_owner = $resourceOwnerMapper->find($resouce_owner_id);
        return $resource_owner;
    }

}
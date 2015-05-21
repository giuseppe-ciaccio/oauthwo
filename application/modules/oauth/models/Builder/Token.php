<?php
/**
 *
 * Token.php,
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 *
 */

/**
 *  Builder class to create Access Tokens
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */

class Oauth_Builder_Token  {

    /**
     * Create the access token from a granting resource owner and a set of
     * granted scopes.  The token is composed of a number of individual
     * access token, one for each distinct RS to be contacted.
     * Each individual token is a JWE of a JWS of a JWT of claims.
     *
     * @param Oauth_Model_ResourceOwner $resource_owner
     * @param string $scopes
     * @return Oauth_Model_Token
     */
    public function create(Oauth_Model_ResourceOwner $resource_owner, $scopes) {

        $code = array();

	$config = new Zend_Config_Ini(realpath(
		APPLICATION_PATH . '/configs/application.ini'), 'production');

	//$log = Zend_Registry::get('log');
	//$log->log("ciao " . ": " . $config->asName,0);

        $token_signer = new Oauth_Builder_JWS($config->privSignKeyFile);
        $token_encrypter = new Oauth_Builder_JWE();

        $access_token = new Oauth_Model_Token();
        $token_data = $this->buildTokenData($scopes);


        foreach ($token_data as $k => $v) {
            $prn = $resource_owner->getReference($k);
            if ($prn) {
                $payload = array(
                    'iss' => $config->asName,
                    'exp' => time() + $config->accTokenValidity,
                    'prn' => $prn,
                    'scope' => implode(" ",$v['scopes']),
                );

                $encoded_payload =  json_encode($payload);
                $signed_token = $token_signer->get_token($encoded_payload);
                // if failed signature, e.g. key not accessible
                if (!$signed_token) return FALSE;
                // take the secret shared between AS and RS, from hex to bytes
                $secret_key = pack('H*', $v['resource_server']->getSecret());
                $token_encrypter->set_key($secret_key);
                $encrypted_token = $token_encrypter->get_token($signed_token);
                // if failed encrypt, e.g. RS secret not accessible
                if (!$encrypted_token) return FALSE;
                $code[$v['resource_server']->getUri()] = array();
                $code[$v['resource_server']->getUri()]['scopes']=implode(" ",$v['scopes']);
                $code[$v['resource_server']->getUri()]['token_portion']=$encrypted_token;
            }
        }

        $access_token->setCode(base64_encode(json_encode($code)));
        $access_token->setType('bearer');
        $access_token->setExpireDate(time() + $config->accTokenValidity);

        return $access_token;
    }


    public function create_rsets(Oauth_Model_ResourceOwner $resource_owner, $rsets, $scopes) {

        $code = array();

	$config = new Zend_Config_Ini(realpath(
		APPLICATION_PATH . '/configs/application.ini'), 'production');

        $token_signer = new Oauth_Builder_JWS($config->privSignKeyFile);
        $token_encrypter = new Oauth_Builder_JWE();

        $access_token = new Oauth_Model_Token();
        $token_data = $this->buildTokenData_WRS($scopes, $rsets);

        foreach ($token_data as $k => $v) {
            $prn = $resource_owner->getReference($k);
            if ($prn) {
                $payload = array(
                    'iss' => $config->asName,
                    'exp' => time() + $config->accTokenValidity,
                    'prn' => $prn,
                    'scope' => implode(" ",$v['scopes']),
                );

                $encoded_payload =  json_encode($payload);
                $signed_token = $token_signer->get_token($encoded_payload);
                // if failed signature, e.g. key not accessible
                if (!$signed_token) return FALSE;
                $secret_key = $v['resource_server']->getSecret();
                $token_encrypter->set_key($secret_key);
                $encrypted_token = $token_encrypter->get_token($signed_token);
                // if failed encrypt, e.g. RS secret not accessible
                if (!$encrypted_token) return FALSE;

//TODO qui usa rset invece di scope
                //$code[$v['endpoint']->getUri()] = array();
                //$code[$v['endpoint']->getUri()]['rsets']=implode(" ",$v['rsets']);
                //$code[$v['endpoint']->getUri()]['token_portion']=$encrypted_token;
		$code[$v['resource_server']->getUri()] = array();
		$code[$v['resource_server']->getUri()]['scopes']=implode(" ",$v['scopes']);
                $code[$v['resource_server']->getUri()]['token_portion']=$encrypted_token;
            }
        }

        $access_token->setCode(base64_encode(json_encode($code)));
        $access_token->setType('bearer');
        $access_token->setExpireDate(time() + $config->accTokenValidity);

        return $access_token;
    }


    /**
     * Uses the set of scopes to retrieve data about the resource servers which
     * delivers and understand each scope.
     *
     * @param string $scopes
     * @return array (associative) of data to be encrypted in the access token
     */
    private function buildTokenData($scopes) {

        $scopes = explode(" ", trim($scopes));
        $datas = array();
        foreach ($scopes as $s) {
            $scopeMapper = new Oauth_Mapper_Scope();
            $scope = $scopeMapper->find($s);

            $serv_id = $scope->getResourceServer()->getId();
            if (array_key_exists($serv_id, $datas)) {
                $datas[$serv_id]['scopes'][] = $scope->getName();
            } else {
                $datas[$serv_id] = array();
                $datas[$serv_id]['scopes'] = array();
                $datas[$serv_id]['scopes'][] = $scope->getName();
                $datas[$serv_id]['resource_server'] = $scope->getResourceServer();
            }
        }

        return $datas;
    }


    private function buildTokenData_WRS($scopes, $rsets) {

        //$scopes = explode(" ", trim($scopes));
        //$rsets = explode(" ", trim($rsets));
        //$datas = array();
        //foreach (array_combine($scopes, $rsets) as $s => $r) {
        //    $scopeMapper = new Resource_Mapper_RSetScopes();
        //    $scopeRSetMapper = new Resource_Mapper_RSetTable();
        //    $scope_rset_couple = $scopeMapper->findrsetscope($r,$s);
        //    $rset = $scopeRSetMapper->find($r);

        //    $endpoint = $rset[0]->getUri();
        //    if (array_key_exists($endpoint, $datas)) {
        //        $datas[$endpoin]['rsets'][] = $scope_rset_couple;
        //    } else {
        //        $datas[$endpoint] = array();
        //        $datas[$endpoint]['rsets'] = array();
        //        $datas[$endpoint]['rsets'][] = $scope_rset_couple;
        //    }
        //}

        $scopes = explode(" ", trim($scopes));
	$datas = array();
	foreach ($scopes as $s) {
		$scopeMapper = new Oauth_Mapper_Scope();
		$scope = $scopeMapper->find($s);

		$serv_id = 'comune_rs_id';
		if (array_key_exists($serv_id, $datas)) {
			$datas[$serv_id]['scopes'][] = $scope->getName();
		} else {
			$datas[$serv_id] = array();
			$datas[$serv_id]['scopes'] = array();
			$datas[$serv_id]['scopes'][] = $scope->getName();
			$datas[$serv_id]['resource_server'] = $scope->getResourceServer();
		}
       	}

        return $datas;
    }

}

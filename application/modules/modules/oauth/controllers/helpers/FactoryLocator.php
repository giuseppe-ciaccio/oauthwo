<?php
/**
 *
 * FactoryLocator.php,
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 *
 */

 require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Builder'. DIRECTORY_SEPARATOR . 'AuthorizationCodeWRset.php');


/**
 *  Extends an abstract helper and is used by controllers to get factories
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Controller_Action_Helper_FactoryLocator extends Zend_Controller_Action_Helper_Abstract{


    /**
     * Returns the correct abstract factory to create a Token
     *
     * @return Oauth_Builder_Token
     */
    public function getTokenFactory(){
        return new Oauth_Builder_Token();
    }


	/**
	 * Returns the correct abstract factory to create an Authorization Code
	 *
	 * @return Oauth_Builder_AuthorizationCode
	 */
	public function getAuthorizationCodeFactory(){
		return new Oauth_Builder_AuthorizationCode();
    }

    /**
     * Returns the correct abstract factory to create an Authorization Code
     *
     * @return Oauth_Builder_AuthorizationCodeWRS
     */
    public function getAuthorizationCodeFactoryWRS(){
        return new Oauth_Builder_AuthorizationCodeWRS();
    }

	/**
	 * Returns the correct factory to create a refresh token
	 *
	 * @return Oauth_Builder_RefreshToken
	 */
	public function getRefreshtokenFactory(){
		return new Oauth_Builder_RefreshToken();
    }

    /**
     * Returns the correct factory to create a refresh token
     *
     * @return Oauth_Builder_RefreshTokenWRS
     */
    public function getRefreshtokenFactoryWRS(){
        return new Oauth_Builder_RefreshTokenWRS();
    }


}

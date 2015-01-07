<?php

/**
 *
 * AuthorizationCode.php,
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 *
 */

/**
 *  Implements an Authorization Code DataBase Table
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_DbTable_AuthorizationCodeWRsets extends Zend_Db_Table_Abstract {

    /**
     * db table name
     *
     * @var string
     */
    protected $_name = 'authorization_codes_wrsets';

    /**
     * primary column name
     *
     * @var string
     */
    protected $_primary = 'authorization_code';

}


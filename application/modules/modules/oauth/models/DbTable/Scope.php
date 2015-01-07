<?php
/**
 * 
 * Scope.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Scope DataBase Table
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_DbTable_Scope extends Zend_Db_Table_Abstract
{
    /**
     * db table name
     * @var string
     */
    protected $_name = 'scope';
    
    /**
     * primary column name
     * @var string
     */
    protected $_primary = 'scope_id';

}

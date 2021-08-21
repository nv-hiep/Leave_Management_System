<?php
/**
 * /role.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Role
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Role extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'name',
    );
    protected static $_table_name = 'account_roles';

    /*
     * Validate for Form input
     *
     * @access public
     * @param  String $name model validate
     * @param object $obj model check validate
     * @return Form validate
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function validate($name, $obj)
    {
        $val = Validation::forge($name);
        $val->add_field('auth', 'Auth', 'required|max_length[64]|min_length[2]|auth_name');
        $val->field('auth')->add_rule('unique_field', 'name', $obj);
        return $val;
    }

    /**
     * Check if auth used
     *
     * @params int $id auth ID
     * @return boolean
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function is_auth_used($id)
    {
        $res = Model_AccountRole::query()
             ->where('role_id', $id)
             ->get();
        if (count($res) > 0) {
            return true;
        }
        return false;
    }

    /**
     * get all roles
     *
     * @param boolean $select show warning string
     * @return array all of roles
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function get_all_roles($select = false)
    {
        $groups = Model_Role::find('all');
        $ret = array();
        foreach ($groups as $val) {
            $ret[$val['id']] = $val['name'];
        }
        if ($select === true) {
            $ret = array('' => __('message.select_role')) + $ret;
        }
        return $ret;
    }

}

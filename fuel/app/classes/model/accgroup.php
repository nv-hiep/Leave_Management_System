<?php

/**
 * /accgroup.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Accgroup
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
class Model_Accgroup extends \Orm\Model
{

    protected static $_table_name = 'account_groups';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'name',
        'user_id',
        'created_at',
        'updated_at'
    );

    /**
     * Create validation object
     *
     * @param string $action action (create|edit)
     * @param object $obj object
     * @return object return a validation object
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function validate($action = null, $obj = null)
    {
        $val = Validation::forge($action);
        $val->add_field('name', __('group.name'), 'required|max_length[16]|min_length[2]');
        $val->field('name')
            ->add_rule('unique_field', 'name', $obj);
        return $val;
    }

    /**
     * get all roles
     *
     * @return array all of position
     *
     * @access public
     * @author NVH
     */
    public static function get_groups()
    {
        $grps = Model_Accgroup::query()
               ->get();
        return $grps;
    }

    /**
     * get all roles
     *
     * @param boolean $select show warning string
     * @return array all of position
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function get_list_perms($select = false)
    {
        $perms = Model_AccountPerms::query()
               ->get();
        $ret = array();
        foreach ($perms as $val) {
            $ret[$val->id] = $val->permission;
        }
        if ($select === true) {
            $ret = array('' => __('message.select_perm')) + $ret;
        }
        return $ret;
    }

    /**
     * get info of all perms
     *
     * @return array all of permissions
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function get_all_info_perms()
    {
        $perms = Model_AccountPerms::query()
               ->get();
        return $perms;
    }

    /**
     * get info of all perms
     *
     * @param string  $perm permission
     * @return array  detail of permission
     *
     * @access public
     * @author
     */
    public static function get_perm_info($perm)
    {
        $info = Model_AccountPerms::query()
               ->where('permission', $perm)
               ->get_one();
        return $info;
    }
}

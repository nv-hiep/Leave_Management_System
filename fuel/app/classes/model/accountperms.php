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
class Model_AccountPerms extends \Orm\Model
{

    protected static $_table_name = 'account_permissions';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'area',
        'permission',
        'description',
        'actions',
        'user_id',
        'created_at',
        'updated_at'
    );

    /*
     * Validate for Form input
     *
     * @access public
     * @param  String $name model validate
     * @param object $object model check validate
     * @return Form validate
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function validate($name, $object)
    {
        $val = Validation::forge($name);
        $val->add_field('area', 'Area', 'required|max_length[16]|min_length[2]');
        $val->field('area')->add_rule('unique_area_perm', $object);

        $val->add_field('perm', 'Permission', 'required|max_length[16]|min_length[2]');
        $val->field('perm')->add_rule('unique_area_perm', $object);

        $val->add_field('act', 'Action', 'required');
        return $val;
    }

    /**
     * get all roles
     *
     * @return array all of position
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function get_all_perms()
    {
        $perms = Model_AccountPerms::query()
               ->get();
        $ret = array();
        foreach ($perms as $val) {
            $ret[$val['id']] = $val;
        }
        return $ret;
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
    public static function get_list_perms()
    {
        $perms = Model_AccountPerms::query()
               ->order_by('permission', 'asc')
               ->get();
        $ret = array();
        foreach ($perms as $val) {
            $ret[$val->id] = $val->permission;
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

    /**
     * Delete role-permissions by permission ID
     *
     * @param int  $perm permission ID
     * @return void
     * @access public
     * @author
     */
    public static function del_roleperm_by_perm_id($perm)
    {
        $del_rows = DB::delete('account_role_permissions')
                ->where('perms_id', $perm)
                ->execute();
        return $del_rows;
    }
}

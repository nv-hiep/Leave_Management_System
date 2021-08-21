<?php

/**
 * /group.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Group
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
class Model_Group extends \Orm\Model
{
    protected static $_table_name ='group';
    protected static $_primary_key = array('account_id', 'group_id');

    protected static $_properties = array(
        'account_id',
        'group_id',
        'primary_group'
    );

    /**
     * relation to Mstgroup table
     *
     * @var property of ORM package
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static $_belongs_to = array(
        'mst_group'          => array(
            'key_from'       => 'group_id',
            'model_to'       => 'Model_Mstgroup',
            'key_to'         => 'id',
            'cascade_delete' => false,
            'cascade_update' => false
        )
    );

    /**
     * get Group
     *
     * @param string $account_id account id
     * @return array Group of an account
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function get_group($account_id)
    {
        $groups = Model_Group::query()
                ->related('mst_group')
                ->where('account_id', $account_id)
                ->where('mst_group.lock', false)->get();

        $ret = array();
        foreach ($groups as $group){
            $ret[] = $group->group_id;
        }
        return $ret;
    }

    /**
     * get all active group based on user
     *
     * @param integer $user_id
     * @return ORM object
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_active_group_by_user($user_id)
    {
        $groups = Model_Group::query()
                ->related('mst_group')
                ->where('account_id', $user_id)
                ->where('mst_group.lock', FALSE)
                ->get();

        $ret = array();
        foreach ($groups as $group){
            $ret[] = $group->group_id;
        }
        return $ret;
    }

    /**
     * get primary group based on user
     *
     * @param integer $user_id User Id
     * @return ORM object $group Primary group's info of User
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_primary_group_from_user_id($user_id)
    {
        $group  = Model_Group::query()
                ->related('mst_group')
                ->where('account_id', $user_id)
                ->where('primary_group', true)
                ->where('mst_group.lock', false)
                ->get_one();
        return $group;
    }

    /**
     * Get account belong to group with condition this group is primary group
     *
     * @param integer $group_id group id
     * @return object ORM object of group
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_user_group($group_id)
    {
        $group = Model_Group::query()
                ->where('group_id', $group_id)
                ->where('primary_group', 1)
                ->get();

        return $group;
    }
}

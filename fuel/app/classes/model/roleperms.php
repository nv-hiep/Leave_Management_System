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
class Model_Roleperms extends \Orm\Model
{

    protected static $_table_name  = 'account_role_permissions';
    protected static $_primary_key = array('id');
    protected static $_properties  = array(
        'id',
        'role_id',
        'perms_id',
        'actions'
    );

    /**
     * relation to Mstrequesttype, Account, mstshiftposition tables
     *
     * @var property of ORM package
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static $_belongs_to = array(
        'role' => array(
            'key_from' => 'role_id',
            'model_to' => 'Model_Role',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_update' => false
        ),
        'perms' => array(
            'key_from' => 'perms_id',
            'model_to' => 'Model_AccountPerms',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_update' => false
        )
    );

    /*
     * Validate for Form input
     *
     * @param  String $name model validate
     * @param object $obj model check validate
     * @return Form validate
     *
     * @access public
     * @author Nguyen Van Hiep
     */

    public static function validate($name)
    {
        $val = Validation::forge($name);
        $val->add_field('role', __('common.role'), 'required');
        $val->add_field('perm', __('common.perm'), 'required');
        $val->add_field('act', __('title.actions'), 'required');

        return $val;
    }

    /**
     * get info of all role-perms
     *
     * @return array all of role-permissions
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function get_all_roleperms()
    {
        $rps = Model_Roleperms::query()
                ->related('role')
                ->related('perms')
                ->get();

        foreach ($rps  as $key => $rp) {
            $actions  = array_flip(unserialize($rp->perms->actions));
            $sel_acts = unserialize($rp->actions);
            $rps[$key]->acts = array_flip(array_intersect($actions, $sel_acts));

        }
        return $rps;
    }

    /**
     * get info of role-perm
     *
     * @params integer $id role-permission ID
     * @return object Detail of role-permission
     *
     * @access public
     * @author nvh
     */
    public static function get_roleperm($id)
    {
        $rp = Model_Roleperms::query()
                ->related('role')
                ->related('perms')
                ->where('id', $id)
                ->get_one();
        return $rp;
    }

}

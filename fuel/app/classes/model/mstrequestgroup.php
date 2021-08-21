<?php

/**
 * /mstrequestgroup.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author nguyen_van_loi
 * @package tmd
 * @since Nov 25, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Model_Mstrequestgroup
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author nguyen_van_loi
 * @package tmd
 * @since Nov 25, 2014
 * @version $Id$
 * @license X
 */
class Model_Mstrequestgroup extends Orm\Model
{

    protected static $_table_name  = 'mst_request_group';
    protected static $_primary_key = array('request_id', 'group_id');
    protected static $_properties  = array(
        'request_id',
        'group_id'
    );

    /**
     * relation to mst_request_type
     *
     * @var property of ORM package
     *
     * @author Dao Anh Minh
     */
    protected static $_belongs_to = array(
        'type' => array(
            'key_from'       => 'request_id',
            'model_to'       => 'Model_Mstrequesttype',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'mst_group' => array(
            'key_from'       => 'group_id',
            'model_to'       => 'Model_Mstgroup',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        )
    );

    /**
     * get request type based on group which user belong to
     *
     * @param mixed $user_groups orm object of groups which user belong to
     * @return mixed orm object of all request type based on group which user belong to
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_request_type_based_on_group($user_groups)
    {
        $types = Model_Mstrequestgroup::query()
                ->related('type')
                ->related('mst_group')
                ->where('group_id', 'in', $user_groups)
                ->where('type.lock', false)
                ->group_by('request_id')
                ->order_by('type.request_name', 'asc')
                ->get();
        return $types;
    }

    /**
     * get group by request type
     *
     * @param integer $type_id request type id
     * @return array $groups groups' Info
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_group_from_request_type($type_id)
    {
        $groups = Model_Mstrequestgroup::query()
                ->related('mst_group')
                ->where('request_id', $type_id)
                ->where('mst_group.lock', false)
                ->get();
        $ret = array();
        foreach ($groups as $group){
            $ret[] = $group->group_id;
        }
        return $ret;
    }

    /**
     * Get related types of a shiftwork-group to check if group is editable
     *
     * @param integer $group_id group id
     * @return array of related types
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_shift_types_related_to_shiftwork_group($group_id)
    {
        $ret = array();
        $selected_group = Model_Mstgroup::get_active_group_by_id($group_id);
        if ($selected_group->shiftwork_flag == NON_SHIFTWORK) {
            return $ret;
        }
        $groups = Model_Mstrequestgroup::query()
                ->related('type')
                ->where('group_id', $group_id)
                ->where('type.lock', false)
                ->get();

        foreach ($groups as $group) {
            if(($selected_group->shiftwork_flag == SHIFTWORK) and ($group->type->date_select_type == SHIFT_CHECKBOX)) {
                $ret[] = $group->type->request_name;
            }
        }
        return $ret;
    }

    /**
     * get request_id Having Group
     *
     * @return AllRequestHavingGroup
     * @param $id request type id
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_requestgroup_by_requestid($id)
    {
        $rarr_id[] = array();
        $query = Model_Mstrequestgroup::query()
                ->related('type', array('where' => array(array('id', '=', $id))))
                ->related('mst_group')
                ->get();
        foreach ($query as $group_id) {
            $rarr_id[] = $group_id->mst_group->id;
        }
        return $rarr_id;
    }

    /**
     * get country abbreviation based on request type
     *
     * @param integer $request_type_id request type id
     * @return array country abbreviation
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_country_abbreviation_based_on_request_type($request_type_id)
    {
        $groups = Model_Mstrequestgroup::query()
                ->related('mst_group')
                ->where('mst_group.lock', false)
                ->where('request_id', $request_type_id)
                ->get();

        $country_abbreviation = array();
        foreach ($groups as $each_group) {
            $country_abbreviation[] = $each_group->mst_group->country;
        }

        return $country_abbreviation;
    }

    /**
     * get group ids based on request type
     *
     * @param integer $request_type_id request type id
     * @return array group ids
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_group_ids_based_on_request_type($request_type_id)
    {
        $groups = Model_Mstrequestgroup::query()
                ->related('mst_group')
                ->where('mst_group.lock', false)
                ->where('request_id', $request_type_id)
                ->get();

        $group_ids = array();
        foreach ($groups as $each_group) {
            $group_ids[] = $each_group->mst_group->id;
        }

        return $group_ids;
    }
}

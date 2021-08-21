<?php

/**
 * /mstgroup.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
use Orm\Model;

/**
 * mstgroup
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Mstgroup extends Model
{

    protected static $_table_name = 'mst_group';
    protected static $_primary_key = array('id');
    protected static $_properties  = array(
        'id',
        'group_name',
        'shiftwork_flag',
        'opening_time',
        'closing_time',
        'saturday_work',
        'sunday_work',
        'holiday_work',
        'country',
        'lock',
        'create_date',
        'up_date'
    );

    /**
     * relation to shift_position
     *
     * @var property of ORM package
     *
     * @author Nguyen Van Hiep
     */
    protected static $_has_many = array(
        'group_position'     => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Shiftposition',
            'key_to'         => 'group_id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'group_worktime'     => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Shiftworktime',
            'key_to'         => 'group_id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'mst_request_group'  => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Mstrequestgroup',
            'key_to'         => 'group_id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
    );

    /**
     * Validate group value from posted form value
     *
     * @param string $name validation name
     * @param array $post POST value
     * @return Validation object
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Dao Anh Minh
     *
     */
    public static function validate($name, $post, $obj)
    {
        $val = Validation::forge($name);
        $val->add_field('group_name', __('group.group_name'), 'required|max_length[64]|min_length[2]');
        $val->field('group_name')->add_rule('unique_group', 'group_name', $obj);
        $val->add_field('shiftwork_flag', __('group.shiftwork_flag'), 'required');
        $val->add_field('country', __('group.country'), 'required');

        // Validattion corresponding with check or not check shift-work
        if (isset($post['opening_time']) and isset($post['closing_time'])) {
            $val->add_field('opening_time', __('group.opening_time'), 'required|time');
            $val->add_field('closing_time', __('group.closing_time'), "required|time|end_time[{$post['opening_time']}]");
            // holiday flag
            $val->add_field('saturday_work', __('group.saturday_work'), 'required');
            $val->add_field('sunday_work', __('group.sunday_work'), 'required');
            $val->add_field('holiday_work', __('group.holiday_work'), 'required');
        } else {
            $val->add('shiftwork', __('group.worktime'))
                ->add_rule('shift_selection');
            $val->add('shift_position', __('common.position'))
                ->add_rule('position_selection');
        }

        return $val;
    }

    /**
     * get shift_worktime by user-group
     *
     * @param integer $user_group group-id
     * @return array $output shift-worktimes
     *
     * @since 1.0
     * @version 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_shiftwork_time_from_group($user_group)
    {
        $worktimes = Model_Mstgroup::query()
                   ->related('group_worktime')
                   ->related('group_worktime.shiftworktimes')
                   ->where('id', $user_group)
                   ->where('group_worktime.shiftworktimes.lock', false)
                   ->get_one();
        $output = array();
        foreach ($worktimes->group_worktime as $worktime) {
            $output[$worktime->shiftworktimes->id]['shiftwork_name'] = $worktime->shiftworktimes->shiftwork_name;
            $output[$worktime->shiftworktimes->id]['opening_time']   = date('H:i', strtotime($worktime->shiftworktimes->opening_time));
            $output[$worktime->shiftworktimes->id]['closing_time']   = date('H:i', strtotime($worktime->shiftworktimes->closing_time));
        }
        return $output;
    }

    /**
     * Check if non-shiftwork group exists in common groups
     *
     * @param array $common_group_ids array of group-ids
     * @return boolean
     *
     * @since 1.0
     * @version 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function has_nonshift_group($common_group_ids)
    {
        $groups = Model_Mstgroup::query()
                ->where('id', 'in', $common_group_ids)
                ->where('shiftwork_flag', NON_SHIFTWORK)
                ->get();
        if (count($groups) > 0) {
            return true;
        }
        return false;
    }

    /**
     * get all mstgroup
     *
     * @return array all of position
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function get_all_mstgroups()
    {
        $groups = Model_Mstgroup::query()
                ->where('lock', '=', false)
                ->order_by('group_name', 'ASC')
                ->get();
        $ret = array();
        foreach ($groups as $val) {
            $ret[$val->id] = $val->group_name;
        }
        return $ret;
    }

    /**
     * Get all group which have lock is false
     *
     * @return object group object
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_active_group()
    {
        $all_groups = Model_Mstgroup::query()
                ->where('lock', '=', false)
                ->order_by('group_name', 'ASC')
                ->get();
        return $all_groups;
    }

    /**
     * Get all group as orm object
     *
     * @return object group object
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_group_as_object()
    {
        $all_groups = Model_Mstgroup::query()
                ->order_by('group_name', 'ASC')
                ->get();
        return $all_groups;
    }

    /**
     * Get group which have lock is false by id
     *
     * @param integer $group_id group id
     * @return object
     *
     * @access public
     * @author Dao Anh Minh
     *
     * @version 1.0
     * @since 1.0
     */
    public static function get_active_group_by_id($group_id)
    {
        $group = Model_Mstgroup::find($group_id, array(
                    'where' => array('lock' => false)
        ));
        return $group;
    }

    /**
     * Get information about country, such as country's name, google api key, google id
     *
     * @return array information about country
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_country_info()
    {
        $file_path = USER_CONFIG_PATH . '/calendar.ini';
        $ret = array();

        // if calendar file does not exist -> default country is Japan
        if (!File::exists($file_path)) {
            $ret['ja'] = __('country.ja');
            return $ret;
        }

        // 1. If information about countries exists -> get countries's information from calendar.ini
        // 2. Remove google_api_key info
        $country_info = parse_ini_file(USER_CONFIG_PATH . '/calendar.ini', true);
        unset($country_info['google_api_key']);

        foreach ($country_info as $key => $val) {
            $nation = __("country.$key");
            // If country exists in laguage.ini file -> show!
            if(isset($nation) and strlen($nation)) {
                $ret[$key] = $nation;
            } else { // if key [counry_name] exits in calendar.ini -> show!
                if (isset($val['country_name']) and strlen($val['country_name'])) {
                    $ret[$key] = $val['country_name'];
                } else {  // if key [counry_name] does not exist -> display abbreviation of the country [us,ja,vn]
                    $ret[$key] = $key;
                }
            }
        }

        return $ret;
    }

    /**
     * Get abbreviation of the country [us,ja,vn] based on group ids
     *
     * @param array $group_ids groups ids
     * @return array abbreviation of the country [us,ja,vn]
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_country_by_group($group_ids)
    {
        // get countries by group ids
        $countries = Model_Mstgroup::query()
                ->where('id', 'IN', $group_ids)
                ->get();

        // fetch countries to array
        $ret = array();
        foreach ($countries as $key => $val) {
            if (!in_array($val['country'], $ret)) {
                $ret[] = $val['country'];
            }
        }

        return $ret;
    }

    /**
     * get group by request_type
     *
     * @param int $request_id request_type id
     *
     * @return array group
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_mstgroup_by_request_id($request_id)
    {
        $groups = Model_Mstgroup::query()
                ->related('mst_request_group')
                ->where('mst_request_group.request_id', '=', $request_id)
                ->where('lock', '=', false)
                ->order_by('group_name', 'ASC')
                ->get();
        return $groups;
    }

    /**
     * Get workingtime of a group
     *
     * @param integer $group_id group id
     * @return array opening-time and closing-time of a group
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_workingtime_of_group($group_id)
    {
        $group = Model_Mstgroup::query()
                ->where('id', $group_id)
                ->get_one();

        if ($group->shiftwork_flag == false) {
            return array(
                'opening_time' => date('H:i', strtotime($group->opening_time)),
                'closing_time' => date('H:i', strtotime($group->closing_time))
            );
        }

        $worktime = Model_Shiftworktime::get_worktime_by_group($group_id);

        return array(
            'opening_time' => $worktime['opening_time'],
            'closing_time' => $worktime['closing_time']
        );
    }

    /**
     * Check if there's at least one shiftwork_group in given groups
     *
     * @param array $group_ids group IDs
     * @return boolean true | fasle
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function exist_shiftwork_group($group_ids)
    {
        $groups = Model_Mstgroup::query()
                ->where('id', 'IN', $group_ids)
                ->get();
        foreach ($groups as $group) {
            if($group->shiftwork_flag == true) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get working days of groups
     *
     * @param array $group_ids group IDs
     * @return array working_days of groups (default: Monday to Friday)
     * Check if Saturday, Sunday & Holidays are working-days, if yes, add them to working-days
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_working_days_from_groups($group_ids)
    {
        $groups = Model_Mstgroup::query()
                ->where('id', 'IN', $group_ids)
                ->get();
        $ret = array();
        foreach ($groups as $group) {
            if($group->saturday_work == true) {
                $ret[] = 'saturday';
            }
            if($group->sunday_work == true) {
                $ret[] = 'sunday';
            }
            if($group->holiday_work == true) {
                $ret[] = 'holiday';
            }
        }
        return array_unique($ret);
    }

    /**
     * Get resting days of groups
     *
     * @param array $group_ids group IDs
     * @return array working_days of groups
     * Check if Saturday, Sunday & Holidays are resting-days, if yes, add them to working-days
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_resting_days_from_groups($group_ids)
    {
        $groups = Model_Mstgroup::query()
                ->where('id', 'IN', $group_ids)
                ->get();
        $ret = array();
        foreach ($groups as $group) {
            if($group->saturday_work == false) {
                $ret[] = 'saturday';
            }
            if($group->sunday_work == false) {
                $ret[] = 'sunday';
            }
            if($group->holiday_work == false) {
                $ret[] = 'holiday';
            }
        }
        return array_unique($ret);
    }

    /**
     * get list group name by position
     *
     * @param int $shift_position_id id of shift_position
     *
     * @return string list group_name of mst_group
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_list_group_name_by_position($shift_position_id)
    {
        $list_group_name = "";
        $groups = Model_Mstgroup::query()
                ->related('group_position')
                ->where('group_position.shift_position_id', '=', $shift_position_id)
                ->where('lock', '=', false)
                ->order_by('group_name', 'ASC')
                ->get();
        if (count($groups) > 0) {
            $res = array();
            foreach ($groups as $val) {
                $res[] = $val->group_name;
            }
            $list_group_name = implode(', ', $res);
        }
        return $list_group_name;
    }

    /**
     * get group name by shift
     *
     * @param int $id id of mst_shift_worktime
     *
     * @return string list group_name of mst_group
     *
     * @access public
     * @author Nguyen Van LoI
     */
    public static function get_list_group_name_by_shift($id)
    {
        $list_group_name = "";
        $groups = Model_Mstgroup::query()
                ->related('group_worktime')
                ->where('lock', false)
                ->where('group_worktime.shift_worktime_id', $id)
                ->order_by('group_name', 'ASC')
                ->get();
        if (count($groups) > 0) {
            $res = array();
            foreach ($groups as $val) {
                $res[] = $val->group_name;
            }
            $list_group_name = implode(', ', $res);
        }
        return $list_group_name;
    }

}

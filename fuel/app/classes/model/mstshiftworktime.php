<?php

/**
 * /Mstshiftworktime.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Model_Mstshiftworktime
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
class Model_Mstshiftworktime extends Orm\Model
{

    protected static $_table_name = 'mst_shift_worktime';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'shiftwork_name',
        'opening_time',
        'closing_time',
        'create_date',
        'up_date',
        'lock'
    );

    /*
     * Validate for Form input
     *
     * @param String $name validate
     * @param object $obj model check validate
     * @return Form validate
     *
     * @author Nguyen Van Loi
     */

    public static function validate($name, $obj, $shiftworktime_post)
    {
        $val = Validation::forge($name);
        $val->add('shiftwork_name', __('shift.shiftwork_name'))
             ->add_rule('required')
             ->add_rule('min_length', 2)
             ->add_rule('max_length', 64);
        $val->add_field('opening_time', __('group.opening_time'), 'required|time');
        $val->add_field('closing_time', __('group.closing_time'), "required|time|end_time[{$shiftworktime_post['opening_time']}]");
        $val->field('shiftwork_name')->add_rule('unique_position', 'shiftwork_name', $obj);
        return $val;
    }

    /**
     * Get all active work time
     *
     * @return object ORM object of work time
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_work_time()
    {
        $work_times = Model_Mstshiftworktime::query()
                ->where('lock', false)
                ->get();
        return $work_times;
    }

}

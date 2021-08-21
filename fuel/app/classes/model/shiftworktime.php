<?php

/**
 * /shiftworktime.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 18, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * shiftworktime
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 18, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Shiftworktime extends \Orm\Model
{
    protected static $_table_name = 'shift_worktime';
    protected static $_primary_key = array('group_id','shift_worktime_id');

    protected static $_properties = array(
        'group_id',
        'shift_worktime_id'
    );

    /**
     * relation to mst_shift_worktime, mst_group
     *
     * @var property of ORM package
     *
     * @author Nguyen Van Hiep
     */
    protected static $_belongs_to = array(
        'shiftworktimes' => array(
            'key_from'       => 'shift_worktime_id',
            'model_to'       => 'Model_Mstshiftworktime',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'mst_group'      => array(
            'key_from'       => 'group_id',
            'model_to'       => 'Model_Mstgroup',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        )
    );

    public static function get_worktime_by_group($group_id)
    {
        $worktimes = Model_Shiftworktime::query()
                   ->related('shiftworktimes')
                   ->where('group_id', $group_id)
                   ->where('shiftworktimes.lock', false)
                   ->order_by('shiftworktimes.shiftwork_name', 'desc')
                   ->get();
        if(count($worktimes) > 1) {
            return array(
                'opening_time' => OPENING_TIME,
                'closing_time' => CLOSING_TIME
            );
        }
        $worktime = reset($worktimes);
        return array(
            'opening_time' => date('H:i', strtotime($worktime->shiftworktimes->opening_time)),
            'closing_time' => date('H:i', strtotime($worktime->shiftworktimes->closing_time))
        );
    }

}
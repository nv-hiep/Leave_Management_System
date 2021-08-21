<?php

/**
 * /shiftworktime.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Jan 09, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Shiftposition
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Jan 09, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Shiftposition extends \Orm\Model
{
    protected static $_table_name = 'shift_position';
    protected static $_primary_key = array('group_id','shift_position_id');

    protected static $_properties = array(
        'group_id',
        'shift_position_id'
    );

    /**
     * relation to mst_shift_position, mst_group
     *
     * @var property of ORM package
     *
     * @author Nguyen Van Hiep
     */
    protected static $_belongs_to = array(
        'shiftpositions' => array(
            'key_from'       => 'shift_position_id',
            'model_to'       => 'Model_Mstshiftposition',
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

    /**
     * get all active groups of a user
     *
     * @param array $user_groups Group IDs
     * @return array of ORM objects
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_position_by_group($user_groups)
    {
        $positions = Model_Shiftposition::query()
                ->related('shiftpositions')
                ->where('group_id', 'in', $user_groups)
                ->where('shiftpositions.lock', false)
                ->get();
        $rets = array();
        if(count($positions) == 0) {
            return $rets;
        }
        foreach ($positions as $position) {
            $rets[] = $position->shift_position_id;
        }
        $pos = Model_Mstshiftposition::get_positions_from_ids(array_unique($rets));

        return $pos;
    }
}

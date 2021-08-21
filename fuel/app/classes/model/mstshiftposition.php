<?php

/**
 * /mstshiftposition.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author nguyen_van_loi
 * @package tmd
 * @since Nov 20, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Model_Mstshiftposition
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author nguyen_van_loi
 * @package tmd
 * @since Nov 20, 2014
 * @version $Id$
 * @license X
 */
class Model_Mstshiftposition extends Orm\Model
{

    protected static $_table_name = 'mst_shift_position';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'position_name',
        'lock',
        'create_date',
        'up_date'
    );

    /*
     * Validate for Form input
     *
     * @access public
     * @param  String $name model validate
     * @param object $obj model check validate
     * @return Form validate
     *
     * @access public
     * @author Nguyen Van Loi
     */

    public static function validate($name, $obj)
    {
        $val = Validation::forge($name);
        $val->add_field('position_name', __('shift.position_name'), 'required|max_length[64]|min_length[2]');
        $val->field('position_name')->add_rule('unique_position', 'position_name', $obj);
        return $val;
    }

    /**
     * get all active shift positions
     *
     * @return object ORM object of mst_shift_postion
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_position()
    {
        $positions = Model_Mstshiftposition::query()
                ->where('lock', false)
                ->get();

        return $positions;
    }

    /**
     * Get active shift-positions
     *
     * @param array $position_ids position-IDs
     * @return array of ORM objects : position infos
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_positions_from_ids($position_ids)
    {
        $positions = Model_Mstshiftposition::query()
                ->where('id', 'in', $position_ids)
                ->where('lock', false)
                ->get();
        $pos = array();
        foreach ($positions as $position) {
                $pos[$position->id] = $position->position_name;
            }
        return $pos;
    }

}

<?php

/**
 * /mstholiday.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 19, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
use Orm\Model;
/**
 * mstholiday
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 19, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Mstholiday extends Model
{
    protected static $_table_name = 'mst_holiday';
    protected static $_primary_key = array('id');

    protected static $_properties = array (
        'id',
        'holiday'
    );

    /**
     * made a relation to holiday table
     *
     * @var ORM relation property
     *
     * @author Dao Anh Minh
     * @access protected
     */
    protected static $_has_many = array(
        'holidays' => array (
            'key_from'       => 'id',
            'model_to'       => 'Model_Holiday',
            'key_to'         => 'holiday_id',
            'cascade_update' => false,
            'cascade_delete' => false
        )
    );

    /**
     * Validate form value
     *
     * @param String $name name of validation
     * @param object $obj model to check validation
     * @return object Validation object
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Dao Anh Minh
     */
    public static function validate($name, $obj)
    {
        $val = Validation::forge($name);

        $val->add_field('holiday', __('common.date'), 'required|date');
        $val->field('holiday')->add_rule('unique_holiday', 'holiday', $obj);
        $val->add('group', __('common.target_group'))
            ->add_rule('group_selection')
            ->add_rule('groups_exist');

        return $val;
    }

    /**
     * Get all holiday in mst_holiday from current date
     *
     * @return object holiday orm object
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_holiday_from_current_date()
    {
        $current_date = date('Y-m-d',  time());

        $holidays = Model_Mstholiday::find('all', array(
            'where' => array(
                array('holiday', '>=', $current_date)
            ),
            'order_by' => array('holiday'=> 'ASC')
        ));

        return $holidays;
    }

    /**
     * Get all holiday in mst_holiday
     *
     * @return object holiday orm object
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_holiday()
    {
        $all_holiday = Model_Mstholiday::find('all', array(
            'order_by' => array('holiday'=>'ASC')
        ));

        return $all_holiday;
    }

    /**
     * Get holiday by date
     *
     * @param string $date string of date
     * @return object mst_holiday orm object
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_holiday_by_date($date)
    {
        $holiday = Model_Mstholiday::query()
                ->where('holiday','=', date('Y-m-d',  strtotime($date)))
                ->get();
        return $holiday;
    }
}

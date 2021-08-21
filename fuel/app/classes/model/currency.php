<?php

/**
 * /currency.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Currency
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
class Model_Currency extends \Orm\Model
{
    protected static $_table_name ='currency';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'code',
        'symbol',
        'name',
        'rate',
        'created_at',
        'updated_at'
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

        $val->add_field('name', __('common.name'), 'required');
        $val->add_field('symbol', __('title.symbol'), 'required');
        $val->add_field('rate', __('title.rate'), 'required');
        $val->add_field('code', __('title.code'), 'required|exact_length[3]');
        $val->field('code')->add_rule('unique_field', 'code', $obj);

        return $val;
    }

    /**
     * Get currencies
     *
     * @return array ORM objects of currencies
     *
     * @access public
     * @author Nguyen Van hiep
     */
    public static function get_currencies($code = false)
    {
        $currs = Model_Currency::query()
                ->get();
        if ($code) {
            $out = array();
            foreach ($currs as $curr) {
                $out[$curr->code] = $curr->name;
            }

            return $out;
        }
        return $currs;
    }

    /**
     * Get currencies for header
     *
     * @return array ORM objects of currencies
     *
     * @access public
     * @author Nguyen Van hiep
     */
    public static function get_header_currencies($code = false)
    {
        $currs = Model_Currency::query()
                ->get();
        $out = array('' => array(
                                    'name'   => 'Currency :',
                                    'symbol' => ''
                                ));
        foreach ($currs as $curr) {
            $out[$curr->code] = array(
                                    'name'   => $curr->name,
                                    'symbol' => $curr->symbol
                                );
        }
        return $out;
    }

    /**
     * Get currencies
     *
     * @return integer $id Currency ID
     * @return object ORM objects of currency
     *
     * @access public
     * @author Nguyen Van hiep
     */
    public static function get_currency_by_id($id)
    {
        $curr = Model_Currency::query()
              ->where('id', $id)
              ->get_one();
        return $curr;
    }
}

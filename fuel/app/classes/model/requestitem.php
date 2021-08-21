<?php

/**
 * /requestitem.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 27, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Requestitem
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 27, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Requestitem extends \Orm\Model
{

    protected static $_table_name  = 'request_item';
    protected static $_primary_key = array('request_id', 'item_order');
    protected static $_properties  = array(
        'request_id',
        'input_name',
        'input_value',
        'item_order',
    );

    /**
     * get reasons of a request
     *
     * @param integer $request_id request ID
     * @param integer $request_type request-type ID
     * @return array $ret array of reasons
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_reason_by_request($request_id, $request_type)
    {
        $reasons = Model_Requestitem::find('all', array(
                    'where'    => array('request_id' => $request_id),
                    'order_by' => array('item_order' => 'asc')
        ));
        $ret = array();
        foreach ($reasons as $reason) {
            $item = Model_Mstrequestitem::get_item_by_request_type_and_item_order($request_type, $reason->item_order);
            if ($item) {
                $ret[$item->input_type] = $reason['input_value'];
            }
        }
        return $ret;
    }
}
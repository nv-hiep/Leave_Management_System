<?php

/**
 * /mstrequestitem.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * mstrequestitem
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Loi
 * @package tmd
 * @since Nov 24, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Mstrequestitem extends Orm\Model
{

    protected static $_table_name = 'mst_request_item';
    protected static $_primary_key = array('request_id', 'item_order');
    protected static $_properties = array(
        'request_id',
        'input_name',
        'required_flag',
        'input_type',
        'option',
        'item_order'
    );

    /**
     * Validate for Form input
     *
     * @param type $name name model validate
     * @return $val validate
     *
     * @access public
     * @version 1.0
     * @since 1.0
     * @author Nguyen Van Loi
     */
    public static function validate($name)
    {
        $val = Validation::forge($name);
        $val->add_field('input_name', __('approval.input_name'), 'required|max_length[64]|min_length[1]');
        return $val;
    }

    /**
     * getAllMstRequestGroup
     *
     * @param  $id id
     * @return array MstRequestGroup
     *
     * @access public
     * @version 1.0
     * @since 1.0
     * @author Nguyen Van Loi
     */
    public static function get_mst_requestitem_by_requestid($id)
    {
        $querys = Model_Mstrequestitem::query()
                ->where('request_id', '=', $id)
                ->get();
        return $querys;
    }

    /**
     * get items by request
     *
     * @param string $request_id Request ID
     * @return array $items Item info
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_items_by_request($request_id)
    {
        $items = Model_Mstrequestitem::find('all', array(
                    'where'    => array('request_id' => $request_id),
                    'order_by' => array('item_order' => 'asc')
        ));
        foreach ($items as $key => $item) {
            if ($item->input_type == SELECT) {
                if (strlen($item->option) > 0) {
                    $item->option = explode(PHP_EOL, $item->option);
                } else {
                    unset($items[$key]);
                }
            }
        }
        return $items;
    }

    /**
     * get item by request-type and item order
     *
     * @param integer $request_id Request ID
     * @param integer $order Item order
     * @return array $items Item info
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_item_by_request_type_and_item_order($request_id, $order)
    {
        $item = Model_Mstrequestitem::query()
              ->where('request_id', '=', $request_id)
              ->where('item_order', '=', $order)
              ->get_one();
        return $item;
    }

    /**
     * get max item order of requestitem
     *
     * @param int $request_id request_id
     * @return int max item order
     *
     * @access public
     * @version 1.0
     * @since 1.0
     * @author Nguyen Van Loi
     */
    public static function get_max_item_order_requestitem($request_id)
    {
        $querys = Model_Mstrequestitem::query()
                ->where('request_id', '=', $request_id);
        return $querys->max('item_order');
    }

    /**
     * get_requestitem_delete
     *
     * @param array $arr_request_item request item
     * @return array $items Item info
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_requestitem_delete($request_id, $arr_request_item)
    {
        $item_post = array();
        for ($i = 0; $i < count($arr_request_item) - 1; $i++) {
            $requestitem = $arr_request_item[$i];
            $item_post[] = $requestitem['item_order'];
        }
        $mstrequestitem_delete = Model_Mstrequestitem::query()
                ->where('request_id', '=', $request_id)
                ->from_cache(false);
        if ($item_post != null) {
            $mstrequestitem_delete->where('item_order', 'not in', $item_post);
        }
        $res = $mstrequestitem_delete->get();
        return $res;
    }

}

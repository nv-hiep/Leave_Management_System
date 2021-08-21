<?php

/**
 * /mstrequesttype.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 24, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Mstrequesttype
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 24, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Mstrequesttype extends \Orm\Model
{

    protected static $_table_name = 'mst_request_type';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'request_name',
        'target_counter',
        'calculation',
        'date_select_type',
        'value_select_type',
        'request_date_type',
        'lock',
        'create_date',
        'up_date',
    );

    /**
     * relation to mst_request_group
     *
     * @var property of ORM package
     *
     * @author Nguyen Van Hiep
     */
    protected static $_has_many = array(
        'request_group' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Mstrequestgroup',
            'key_to'         => 'request_id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'request' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Request',
            'key_to'         => 'request_id',
            'cascade_save'   => false,
            'cascade_update' => false
        )
    );

    /**
     * Validate fields
     *
     * @param string $factory (create|update)
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van Hiep
     */
    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        if (Input::post('items') != null) {
            foreach (Input::post('items') as $key => $item) {
                $val->add('items.' . $key . '.name', 'type.request_item_name')
                        ->add_rule("required");
            }
        }
        $val->add_field('request_name', __('type.request_type_name'), 'required|max_length[64]|min_length[2]');

        return $val;
    }

    /**
     * get all request types with order sorted by name
     *
     * @return array $ret all request-types
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_all_types()
    {
        $types = Model_Mstrequesttype::query()
                ->where('lock', false)
                ->order_by('request_name', 'asc')
                ->get();

        $ret = array();
        $ret[ALL_REQUEST_TYPES] = __('type.all_request_types');
        foreach ($types as $type) {
            $ret[$type['id']] = $type['request_name'];
        }
        return $ret;
    }

    /**
     * get active request type by id
     *
     * @param integer $type_id Request-type ID
     * @return orm object $result Active request-type
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_active_request_type_by_id($type_id)
    {
        $result = Model_Mstrequesttype::query()
                ->where('id', $type_id)
                ->where('lock', FALSE)
                ->get_one();

        return $result;
    }

    /**
     *
     * getMaxIdRequestType
     *
     * @return MaxIdRequestType
     *
     * @access public
     * @version 1.0
     * @since 1.0
     * @author Nguyen Van Loi
     */
    public static function get_max_id_mstrequesttype()
    {
        $querys = Model_Mstrequesttype::query();
        return $querys->max('id');
    }

    /**
     *
     * check type have request pedding
     *
     * @param int $id id request type
     *
     * @return Boolean true|false
     *
     * @access public
     * @version 1.0
     * @since 1.0
     * @author Nguyen Van Loi
     */
    public static function is_type_have_request_pedding($id)
    {
        $number_request = 0;
        $type = Model_Mstrequesttype::query()
                ->related('request')
                ->where('id', '=', $id)
                ->where('request.approval_status', '=', REQUEST_NEW)
                ->get_one();
        if ($type != null) {
            $number_request = count($type->request);
        }
        return $number_request > 0;
    }

}

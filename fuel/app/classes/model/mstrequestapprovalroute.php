<?php

/**
 * /mstrequestapprovalroute.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Loi
 * @package tmd
 * @since Nov 25, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Model_Mstrequestapprovalroute
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Loi
 * @package tmd
 * @since Nov 25, 2014
 * @version $Id$
 * @license X
 */
class Model_Mstrequestapprovalroute extends Orm\Model
{

    protected static $_table_name = 'mst_request_approval_route';
    protected static $_primary_key = array('request_id', 'account_id');
    protected static $_properties = array(
        'request_id',
        'account_id',
        'approval_order'
    );

    /**
     * made a relation to mstrequestapprovalroute table
     *
     * @var ORM relation property
     *
     * @author Nguyen Van Loi
     * @access protected
     */
    protected static $_belongs_to = array(
        'mst_request_type'   => array(
            'key_from'       => 'request_id',
            'model_to'       => 'Model_Mstrequesttype',
            'key_to'         => 'id',
            'cascade_delete' => false,
            'cascade_update' => false
        ),
        'account'            => array(
            'key_from'       => 'account_id',
            'model_to'       => 'Model_Account',
            'key_to'         => 'id',
            'cascade_delete' => false,
            'cascade_update' => false
        )
    );

    /**
     * Get approval route of a request-type
     *
     * @param integer $type_id request-type ID
     * @return string fullname, order of approvers in route, accountant at the end
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_approval_route($type_id)
    {
        $route = Model_Mstrequestapprovalroute::query()
                ->related('account')
                ->where('request_id', $type_id)
                ->order_by('approval_order')
                ->get();
        $output = '';
        foreach ($route as $approver) {
            $output .= $approver->account->last_name . ' ' . $approver->account->first_name . ' > ';
        }
        $output = $output . __('common.accountant');
        return $output;
    }

    /**
     * Get approval order of an approver from a specific request-type
     *
     * @param integer $type_id request-type ID
     * @param integer $approver_id Approver-account ID
     * @return string fullname, order of approvers in route, accountant at the end
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_approver_order_by_request_type($type_id, $approver_id)
    {
        $result = Model_Mstrequestapprovalroute::query()
                ->where('request_id', $type_id)
                ->where('account_id', $approver_id)
                ->get_one();

        return $result;
    }

    /**
     * get approver route from request-type id
     *
     * @param int $id request type id
     * @return object mstrequestapprovalroute
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_route_of_approvers($type_id)
    {
        $querys = Model_Mstrequestapprovalroute::query()
                ->related('account')
                ->related('mst_request_type')
                ->where('mst_request_type.id', $type_id)
                ->order_by('approval_order')
                ->get();
        return $querys;
    }

    /**
     * Get user approval route
     *
     * @param int $id_request_type request type id
     * @param int $app_order order of approval
     * @return array $result user_approvalroute
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_user_approvalroute($id_request_type, $app_order)
    {
        $querys = Model_Mstrequestapprovalroute::query()
                ->where('request_id', $id_request_type)
                ->where('approval_order', $app_order)
                ->get();
        $result = array('request_id' => "", 'account_id' => "");
        foreach ($querys as $value) {
            $result['request_id'] = $value->request_id;
            $result['account_id'] = $value->account_id;
        }
        return $result;
    }

    /**
     * get Account Having Approval
     *
     * @param int $id request type id
     * @return object mstrequestapprovalroute
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_account_approvalroute($id)
    {
        $querys = Model_Mstrequestapprovalroute::query()
                ->related('account')
                ->related('mst_request_type', array('where' => array(array('id', '=', $id))))
                ->where('account.lock', false)
                ->order_by('approval_order')
                ->get();
        return $querys;
    }

    /**
     * Get request approve
     *
     * @param int $request_id request id
     * @param int $account_login account_id of user approval
     * @param int $account_id account id
     * @return array request not approved

     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_request_not_approve($request_id, $account_login, $account_id)
    {
        $list_user = Model_Request::query()
                ->related('request_approval')
                ->related('account')
                ->where('t2.lock', '=', false)
                ->where('t1.account_id', '=', $account_login)
                ->where('t1.status', '=', NOT_APPROVED)
                ->where('t0.request_id', '=', $request_id)
                ->where('t0.account_id', '=', $account_id)
                ->where('t0.approval_status', '=', REQUEST_NEW)
                ->from_cache(false)
                ->group_by('account_id')
                ->get();
        return $list_user;
    }

    /**
     * check exist  user register request
     *
     * @param int $request_id request id
     * @param int $account_id account id
     *
     * @return boolean true|false true: have user, false: not user

     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function is_user_register_request($request_id, $account_id)
    {
        $user = Model_Request::query()
                ->related('request_approval')
                ->related('account')
                ->where('account.lock', '=', false)
                ->where('request_approval.status', '=', NOT_APPROVED)
                ->where('request_id', '=', $request_id)
                ->where('account_id', '=', $account_id)
                ->where('approval_status', '=', REQUEST_NEW)
                ->get();
        return count($user) > 0;
    }

    /**
     * get account of request approval route
     *
     * @param int $request_id request_type id
     *
     * @return array account
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_account_route_by_request_id($request_id)
    {
        $accounts = Model_Mstrequestapprovalroute::query()
                ->related('account')
                ->where('request_id', '=', $request_id)
                ->where('account.lock', '=', false)
                ->order_by('approval_order', 'ASC')
                ->get();
        return $accounts;
    }

    /**
     * Get user approved last end in approval route
     *
     * @param integer $type_id request-type ID
     * @return string fullname, order of approvers in route, accountant at the end
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_user_approved_last_end_route($type_id)
    {
        //get user approved last end
        $approval_route = Model_Mstrequestapprovalroute::query()
                ->related('account')
                ->where('request_id', $type_id)
                ->where('account.lock', false);
        $index_route = $approval_route->max('approval_order');
        //get full name of user approved last end
        $full_name = "";
        $user_approved = Model_Mstrequestapprovalroute::query()
                ->related('account')
                ->where('request_id', $type_id)
                ->where('account.lock', false)
                ->where('approval_order', '<=', $index_route)
                ->order_by('approval_order')
                ->get();
        if (count($user_approved) > 0) {
            $res = array();
            foreach ($user_approved as $value) {
               $res[] = $value->account->last_name . " " . $value->account->first_name;
            }
            $full_name = implode(', ', $res);
        }
        return $full_name;
    }

}

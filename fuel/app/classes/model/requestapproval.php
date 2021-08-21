<?php

/**
 * /requestapproval.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 28, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * requestapproval
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 28, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Requestapproval extends \Orm\Model
{

    protected static $_table_name = 'request_approval';
    protected static $_primary_key = array('request_id', 'account_id');
    protected static $_properties = array(
        'request_id',
        'account_id',
        'status',
        'approval_date'
    );

    /**
     * relation to Account, Request tables
     *
     * @var property of ORM package
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static $_belongs_to = array(
        'account' => array(
            'key_from'       => 'account_id',
            'model_to'       => 'Model_Account',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'request' => array(
            'key_from'       => 'request_id',
            'model_to'       => 'Model_Request',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        )
    );

    /**
     * Get Approvel-route from a request.
     *
     * @params $request_id request id
     * @return $querys array requestapproval
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_approval_route_by_request_id($request_id)
    {
        $request_route = Model_Requestapproval::query()
                ->related('account')
                ->where('request_id', $request_id)
                ->order_by('approval_date', 'asc')
                ->get();
        return $request_route;
    }

    /**
     * get approver confirmed by request id
     *
     * @params integer $request_id Request ID
     * @return boolean updated sucessfully or not
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function update_approval_status_by_request($approver_id, $request_id)
    {
        // Delete all approvers who have NOT approved.
        DB::delete('request_approval')
        ->where('status', NOT_APPROVED)
        ->where('request_id', $request_id)
        ->execute();

        // Delete if exists
        Model_Requestapproval::query()
        ->where('request_id', $request_id)
        ->where('account_id', $approver_id)
        ->delete();

        // Add approver who has forced-approval authority to DB.
        $row = Model_Requestapproval::forge();
        $row->request_id = $request_id;
        $row->account_id = $approver_id;
        $row->status     = APPROVED;
        $row->approval_date = date('Y-m-d h:i:s', time());
        return $row->save();
    }

    /**
     * Get user approval last end of route
     *
     * @params $request_type_id request type id
     * @return approval_order
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_user_approval_last_end_route($request_type_id)
    {
        $requestroute = Model_Mstrequestapprovalroute::query()
                ->related('account')
                ->where('request_id', '=', $request_type_id)
                ->where('account.lock', '=', false);
        return $requestroute->max('approval_order');
    }

    /**
     * Get Request follow User And Type
     *
     * @params $request_id request_id
     * @return $querys array requestapproval
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_user_approved($request_id)
    {
        $querys = Model_requestapproval::query()
                ->where('request_id', '=', $request_id)
                ->where('status', '=', APPROVED)
                ->get();
        return $querys;
    }

    /**
     * Get Mstrequestapproval
     *
     * @params $request_id request_id
     * @params $account_id account_id
     * @return $querys entity Requestapproval
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_request_approval_by_requestid_and_accountid($request_id, $account_id)
    {
        $querys = Model_requestapproval::query()
                ->where('request_id', '=', $request_id)
                ->where('account_id', '=', $account_id)
                ->get();
        return $querys;
    }
}

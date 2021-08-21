<?php

/**
 * /request.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 24, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Request
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
class Model_Request extends \Orm\Model
{

    protected static $_table_name = 'request';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'request_id',
        'account_id',
        'request_date',
        'opening_time',
        'closing_time',
        'day_off',
        'shift_work',
        'approval_status',
        'completion',
        'create_date',
        'up_date'
    );

    /**
     * relation to Mstrequesttype, Account, mstshiftposition tables
     *
     * @var property of ORM package
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static $_belongs_to = array(
        'request_type'       => array(
            'key_from'       => 'request_id',
            'model_to'       => 'Model_Mstrequesttype',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'account'            => array(
            'key_from'       => 'account_id',
            'model_to'       => 'Model_Account',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        ),
        'mst_shift_position' => array(
            'key_from'       => 'shift_work',
            'model_to'       => 'Model_Mstshiftposition',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_update' => false
        )
    );

    /**
     * relation to Requestapproval, Requestitem tables
     *
     * @var property of ORM package
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static $_has_many = array(
        'request_approval'   => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Requestapproval',
            'key_to'         => 'request_id',
            'cascade_update' => false,
            'cascade_delete' => false
        ),
        'request_item'       => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Requestitem',
            'key_to'         => 'request_id',
            'cascade_update' => false,
            'cascade_delete' => false
        )
    );

    /**
     * get request history of all type in selected month of a user
     *
     * @param integer $user_id user id
     * @param date $month month
     * @param integer $requestTypeId  request type id
     *
     * @access public
     * @author Dao Anh Minh
     * @author Nguyen Van Hiep
     */
    public static function get_request_history_in_month($user_id, $month, $type = ALL_TYPES, $asc_order = true, $out_dates = null)
    {

        $result = Model_Request::query()
                ->related('request_approval')
                ->related('request_type')
                ->related('request_approval.account')
                ->related('request_item')
                ->where('account_id', $user_id);

        //display request histories in register request screen
        if ($out_dates != null) {
            $result->and_where_open()
                    ->or_where(DB::expr('DATE_FORMAT(request_date, "%Y/%m")'), $month)
                    ->or_where(DB::expr('DATE_FORMAT(request_date, "%Y/%m/%d")'), 'IN', $out_dates)
                    ->and_where_close();
        } else { //display request histories in history screen
            $result->where(DB::expr('DATE_FORMAT(request_date, "%Y/%m")'), $month);
        }

        if ($type != ALL_TYPES) {
            $result->where('request_id', $type);
        }

        if ($asc_order == true) {
            $result->order_by('request_date', 'asc');
        } else {
            $result->order_by('request_date', 'desc');
        }

        $requests = $result->get();

        // Loop through each request and sort the order of approvers
        foreach ($requests as $request_key => $request) {
            // Check if request is forcibly approved?
            $is_forced_approval = Model_Request::is_forced_approval($request->request_type->id, $request->id);
            if($is_forced_approval === true) {
                usort($request->request_approval, function($a, $b) {
                    return $a->approval_date > $b->approval_date;
                });
            } else {
                // If not forcibly approved, add orders of approvers as reflected in the approval-route.
                foreach($request->request_approval as $key => $approver) {
                    $order = Model_Mstrequestapprovalroute::get_approver_order_by_request_type($request->request_type->id, $approver->account_id);
                    $requests[$request_key]->request_approval[$key]->order = $order->approval_order;
                }

                // Sort by approval-order
                usort($request->request_approval, function($a, $b) {
                    return $a->order > $b->order;
                });
            }
        }

        return $requests;
    }

    /**
     * Check if user can check specific dates.
     *
     * @param interger $date request ID
     * @param interger $type_id request-type ID
     * @param interger $user_id user ID
     * @return boolean true | false
     *
     * @version 1.0
     * @since 1.0
     * @access protected
     * @author Nguyen Van Hiep
     */
    public static function is_checkable($date, $type_id, $user_id)
    {
        $request  = Model_Request::query()
                  ->where('request_id', $type_id)
                  ->where('account_id', $user_id)
                  ->where(DB::expr('DATE_FORMAT(request_date, "%Y/%m/%d")'), $date)
                  ->get_one();

        if (((count($request) > 0) and ($request->approval_status == REQUEST_REJECTED)) or (count($request) == 0)) {
            return true;
        }

        return false;
    }

    /**
     * set request info to array
     *
     * @param interger $type_id request-type ID
     * @param interger $request_id request ID
     * @return false if not forced approval, key of request-approval if forced approval
     *
     * @version 1.0
     * @since 1.0
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static function is_forced_approval($type_id, $request_id)
    {
        $request_route = Model_Requestapproval::get_approval_route_by_request_id($request_id);
        $type_route    = Model_Mstrequestapprovalroute::get_route_of_approvers($type_id);
        if((count($request_route) == 0) or (count($type_route) == 0)) {
            return false;
        }
        $last_approver_request_route = end($request_route)->account->id;
        $last_approver_type_route    = end($type_route)->account->id;
        $not_same_count              = count($request_route) !== count($type_route);
        $not_same_last_approver      = $last_approver_request_route !== $last_approver_type_route;
        if(($not_same_count === true) or (($not_same_count === false) and ($not_same_last_approver === true))) {
            return true;
        }
        return false;
    }

    /**
     * set request info to array
     *
     * @param array $requests array of request's objects
     * @param boolean $date_key key of returned array is date or request-id
     * @return array $ret array of request info
     *
     * @version 1.0
     * @since 1.0
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static function set_request_info_to_array($requests)
    {
        $ret = array();
        foreach ($requests as $request) {
            $request_date = explode('-', $request->request_date);
            $date = $request_date[1] . '/' . $request_date[2];
            if ($request->opening_time != null) {
                $opening_time = explode(':', $request->opening_time);
                $opening_time = $opening_time[0] . ':' . $opening_time[1];
            } else {
                $opening_time = null;
            }

            if ($request->closing_time != null) {
                $closing_time = explode(':', $request->closing_time);
                $closing_time = $closing_time[0] . ':' . $closing_time[1];
            } else {
                $closing_time = null;
            }

            $reasons = Model_Requestitem::get_reason_by_request($request->id, $request->request_id);
            $type = Model_Mstrequesttype::get_active_request_type_by_id($request->request_id);
            $key = $request_date[0] . '/' . $request_date[1] . '/' . $request_date[2];

            $ret[$key] = array(
                'id'              => $request->id,
                'request_id'      => $request->request_id,
                'request_type'    => $type->request_name,
                'account_id'      => $request->account_id,
                'request_date'    => $date,
                'opening_time'    => $opening_time,
                'closing_time'    => $closing_time,
                'day_off'         => $request->day_off,
                'shift_work'      => $request->shift_work,
                'approval_status' => $request->approval_status,
                'completion'      => $request->completion,
                'create_date'     => $request->create_date,
                'up_date'         => $request->up_date,
                'reasons'         => $reasons,
            );
        }
        return $ret;
    }

    /**
     * get all user and number of user's request need to confirm followed by request type
     *
     * @param integer $requestTypeId request type
     * @return object
     *
     * @access public
     * @author Dao Anh Minh
     * @version 1.0
     * @since 1.0
     */
    public static function get_request_of_user()
    {
        $result = Model_Request::query()
                ->related('account')
                ->select(array(DB::expr('COUNT(account_id)'), 'count_user'), 'account_id')
                ->where('approval_status', '=', REQUEST_APPROVED)
                ->where('completion', '=', REQUEST_NEW)
                ->where('account.lock', '=', FALSE)
                ->order_by('account.username', 'ASC')
                ->group_by('account_id')
                ->get();

        return $result;
    }

    /**
     * get requests by user
     *
     * @param integer $account account ID
     * @param string $start_date start date to get request to confirm (16 of previous month)
     * @param string $end_date end date to get request to confirm (15 of selected month)
     * @return ORM object of request
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_approved_request_of_user($account, $start_date = null, $end_date = null)
    {
        $result = Model_Request::query()
                ->related('request_type')
                ->where('account_id', '=', $account)
                ->where('approval_status', '=', REQUEST_APPROVED)
                ->where('completion', '=', REQUEST_UNCOMPLETE);
        if(($start_date != null) and ($end_date != null)) {
            $result->where('request_date', '>=', date('Y-m-d', strtotime($start_date)))
                   ->where('request_date', '<=', date('Y-m-d', strtotime($end_date)));
        }

        $result->order_by('request_date', 'ASC');
        $ret = $result->get();

        return $ret;
    }

    /**
     * get oldest month have request to confirm
     *
     * @return object ORM object of request
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_oldest_month_have_request_to_confirm($account_id)
    {
        //get oldest date of user's request
        $oldest_request_date = Model_Request::query()
                ->where('account_id', $account_id)
                ->where('approval_status', '=', REQUEST_APPROVED)
                ->where('completion', '=', REQUEST_UNCOMPLETE)
                ->min('request_date');

        //calculate month to display request need to confirm
        if (!$oldest_request_date) { //no request to confirm -> display current month
            $oldest_month_to_view_request = date('Y/m/01', time());
        } elseif (date('d', strtotime($oldest_request_date)) > END_DATE_FOR_CONFIRM_REQUEST) { //if day greater than 16 -> request will be displayed in next month (month + 1)
            $oldest_month_to_view_request = date('Y/m/01', date(strtotime('+1 month', strtotime(date('Y/m/01', strtotime($oldest_request_date))))));
        } else {
            $oldest_month_to_view_request = date('Y/m/01', strtotime($oldest_request_date));
        }
        
        return $oldest_month_to_view_request;
    }

    /**
     * getRequestHavingRequestType
     *
     * @param int $request_id request id
     * @param int $account_login account_id
     * @param array $arr_request_id_approved : request approved
     * @param boolean $flag true|false
     * @return $querys object Request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_request_approve_by_requesttype($request_id, $account_login, $arr_request_id_approved, $flag)
    {
        if ($arr_request_id_approved != null) {
            $querys = Model_Request::query()
                    ->select('request_id', array(DB::expr('COUNT(t0.request_id)'), 'numberrequest'))
                    ->related('request_type')
                    ->related('request_approval')
                    ->related('account')
                    ->where('account.lock', '=', false)
                    ->where('request_approval.account_id', '=', $account_login)
                    ->where('request_approval.status', '=', NOT_APPROVED)
                    ->where('request_id', '=', $request_id)
                    ->where('approval_status', '=', REQUEST_NEW);

            if ($flag) {
                $querys->where('id', 'in', $arr_request_id_approved);
            } else {
                $querys->where('id', 'not in', $arr_request_id_approved);
            }
            $querys->group_by('request_id');
            $querys->order_by('request_date');
            $res = $querys->get();
        } else {
            if ($flag) {
                $res = null;
            } else {
                $querys = Model_Request::query()
                        ->select('request_id', array(DB::expr('COUNT(t0.request_id)'), 'numberrequest'))
                        ->related('request_type')
                        ->related('request_approval')
                        ->related('account')
                        ->where('account.lock', '=', false)
                        ->where('request_approval.account_id', '=', $account_login)
                        ->where('request_approval.status', '=', NOT_APPROVED)
                        ->where('request_id', '=', $request_id)
                        ->where('approval_status', '=', REQUEST_NEW)
                        ->group_by('request_id')
                        ->order_by('request_date')
                        ->get();
                $res = $querys;
            }
        }
        return $res;
    }

    /**
     * get_request_by_user_and_type
     *
     * @param integer $account_id account_id
     * @param integer $request_type_id request_id
     * @param string  $arr_date_in_month date search
     * @return object $res  Request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_request_by_user_and_type($account_id, $request_type_id, $arr_date_in_month)
    {
        $querys = Model_Request::query()
                ->related('mst_shift_position')
                ->related('request_type')
                ->related('account')
                ->related('request_item')
                ->where('account_id', '=', $account_id)
                ->where('request_id', '=', $request_type_id);
        if ($arr_date_in_month != "") {
            $querys->where(DB::expr('DATE_FORMAT(request_date, "%Y/%m/%d")'), 'in', $arr_date_in_month);
        }
        $querys->order_by('request_date');
        $res = $querys->get();
        return $res;
    }

    /**
     * get_request_approved_by_user_and_type
     *
     * @param integer $account_id account_id
     * @param integer $request_id request_id
     * @param string  $arr_date_in_month date search
     * @return object $querys Request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_request_approved_by_user_and_type($account_id_login, $account_id, $request_id, $arr_date_in_month)
    {
        $querys = Model_Request::query()
                ->related('request_approval')
                ->related('mst_shift_position')
                ->related('request_item')
                ->where('request_approval.status', '=', APPROVED)
                ->where('request_approval.account_id', '=', $account_id_login)
                ->where('account_id', '=', $account_id)
                ->where('request_id', '=', $request_id)
                ->order_by('request_date');
        if ($arr_date_in_month != "") {
            $querys->where(DB::expr('DATE_FORMAT(request_date, "%Y/%m/%d")'), 'in', $arr_date_in_month);
        }
        return $querys->get();
    }

    /**
     * Get rejected requests of a user
     *
     * @param integer $user_id User Id
     * @param integr  $request_type Request-type Id
     * @return array  $ret all requests in month
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_rejected_requests($user_id, $request_type)
    {
        $requests = Model_Request::query()
                ->where('account_id', $user_id)
                ->where('request_id', $request_type)
                ->where('approval_status', REQUEST_REJECTED)
                ->get();
        return Model_Request::set_request_info_to_array($requests);
    }

    /**
     * Get requests of a user
     *
     * @param integer $user_id User Id
     * @return array  $ret all requests of a user
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_requests_by_user($user_id)
    {
        $requests = Model_Request::query()
                ->related('account')
                ->related('request_type')
                ->where('account_id', '=', $user_id)
                ->where('account.lock', '=', false)
                ->get();
        return $requests;
    }

    /**
     * Get requests which have NOT been approved yet
     *
     * @param string $month month to view (Y/m)
     * @param integer $staff_id Staff ID
     * @return array Requests that have NOT been approved
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_requests_not_approved_yet($month = null, $staff_id = null)
    {
        $requests = Model_Request::query();
        if ($staff_id !== null) {
            $requests->related('request_type')
                    ->related('request_item')
                    ->related('mst_shift_position')
                    ->where('account_id', $staff_id)
                    ->order_by('request_date', 'desc');
        } else {
            $requests->select(array(DB::expr('COUNT(request_date)'), 'counter'), 'account_id')
                    ->group_by('account_id');
        }

        $requests->related('account')
                ->where('approval_status', REQUEST_NEW)
                ->where('completion', REQUEST_UNCOMPLETE)
                ->where('account.lock', false);

        if ($month !== null) {
            $requests->where(DB::expr('DATE_FORMAT(request_date, "%Y/%m")'), $month);
        }

        $requests->order_by('account.last_name', 'asc')
                ->order_by('account.first_name', 'asc');

        return $requests->get();
    }

    /**
     * get oldest month having request to forcibly approve
     *
     * @param integer $user_id User ID
     * @return string oldest month having request to forcibly approve
     *
     * @version 1.0
     * @since 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_oldest_month_having_requests_to_approve($user_id)
    {
        //get oldest date of user's request
        $oldest_date = Model_Request::query()
                ->where('account_id', $user_id)
                ->where('approval_status', '=', REQUEST_NEW)
                ->where('completion', '=', REQUEST_UNCOMPLETE)
                ->min('request_date');

        //calculate month to display request need to confirm
        if (!$oldest_date) { //no request to confirm -> display current month
            $oldest_month_to_view_request = date('Y/m/01', time());
        } else {
            $oldest_month_to_view_request = date('Y/m/01', strtotime($oldest_date));
        }

        return $oldest_month_to_view_request;
    }

    /**
     * get request id approve have approval_order = 0
     *
     * @param int $request_type request id
     * @param array $account_id account_id
     * @param int $account_login account login
     * @return array $res id of request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_requestapproval_by_user_current($request_type, $account_id, $account_login)
    {
        $res = array();
        $querys = Model_Request::query()
                ->related('request_approval')
                ->related('account')
                ->where('account.lock', '=', false)
                ->where('request_id', '=', $request_type)
                ->where('approval_status', '=', REQUEST_NEW)
                ->where('request_approval.status', '=', NOT_APPROVED)
                ->where('account_id', '=', $account_id)
                ->where('request_approval.account_id', '=', $account_login)
                ->get();

       foreach ($querys as $value) {
            $res[] = $value->id;
        }
        return $res;
    }

    /**
     * Get all request approval with user before
     *
     * @param integer $type_id : Request type Id
     * @param integr  $user_id : User Id
     * @return array $res : request approved with user before
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_all_requestapproval_by_user_before($type_id, $user_id)
    {
        $res = array();
        $querys = Model_Request::query()
                ->related('request_approval')
                ->related('account')
                ->where('account.lock', '=', false)
                ->where('request_id', '=', $type_id)
                ->where('t1.account_id', '=', $user_id)
                ->where('t1.status', '=', APPROVED)
                ->where('t0.approval_status', '=', REQUEST_NEW)
                ->group_by('id')
                ->from_cache(false)
                ->get();
        foreach ($querys as $value) {
            $res[] = $value->id;
        }
        return $res;
    }

    /**
     * get min date of user register request
     *
     * @param int $request_type request id
     * @param array $account_id account_id
     * @param array $account_login id account approval
     *
     * @return date $querys max request_date of request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_min_date_request($request_type, $account_id, $account_login, $arr_request_id_approved)
    {
        $request = Model_Request::find('all', array(
                    'related' => array(
                        'request_approval' => array(
                            'join_type' => 'inner',
                            'join_on' => array(
                                array('status', '=', '0'),
                                array('account_id', '=', $account_login),
                            ),
                        )
                    ),
                    'where' => array(
                        array('request_id' => $request_type),
                        array('account_id' => $account_id),
                        array('approval_status' => REQUEST_NEW),
                    ),
        ));
        //set flag min date
        $min_request_date = '0000-00-00';
        foreach ($request as $value) {
            if (in_array($value->id, $arr_request_id_approved)) {
                $min_request_date = $value->request_date;
                break;
            }
        }
        //get min date
        foreach ($request as $value) {
            if (strtotime($value->request_date) < strtotime($min_request_date) && in_array($value->id, $arr_request_id_approved)) {
                $min_request_date = $value->request_date;
            }
        }
        return $min_request_date;
    }

    /**
     * get request approve have approval_order = 0
     *
     * @param int $request_type request id
     * @param array $account_id account_id
     * @param date $arr_date_in_month date search
     * @return object $querys request approved
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_request_approve1($request_type, $account_id, $account_login, $arr_date_in_month)
    {
        $querys = Model_Request::query()
                ->related('mst_shift_position')
                ->related('request_approval')
                ->related('request_item')
                ->related('account')
                ->where('t4.lock', '=', false)
                ->where('request_id', '=', $request_type)
                ->where('approval_status', '=', REQUEST_NEW)
                ->where('t2.status', '=', NOT_APPROVED)
                ->where('account_id', '=', $account_id)
                ->where('t2.account_id', '=', $account_login);
        if ($arr_date_in_month != "") {
            $querys->where(DB::expr('DATE_FORMAT(request_date, "%Y/%m/%d")'), 'in', $arr_date_in_month);
        }
        $querys->order_by('request_date');
        $requestapproval = $querys->get();
        return $requestapproval;
    }

    /**
     *  get request approve have approval_order > 0
     *
     * @param int   $request_type request id
     * @param int   $account_id account_id
     * @param array $arr_request_id_approved array request id approved
     * @param date  $arr_date_in_month date search
     * @return object $res request approved
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_request_approve2($request_type, $account_id, $account_login, $arr_request_id_approved, $arr_date_in_month)
    {
        if ($arr_request_id_approved != null) {
            $querys = Model_Request::query()
                    ->related('mst_shift_position')
                    ->related('request_approval')
                    ->related('request_item')
                    ->related('account')
                    ->where('t4.lock', '=', false)
                    ->where('request_id', '=', $request_type)
                    ->where('approval_status', '=', REQUEST_NEW)
                    ->where('account_id', '=', $account_id)
                    ->where('t2.account_id', '=', $account_login)
                    ->where('t2.status', '=', NOT_APPROVED)
                    ->where('id', 'in', $arr_request_id_approved);
            if ($arr_date_in_month != "") {
                $querys->where(DB::expr('DATE_FORMAT(request_date, "%Y/%m/%d")'), 'in', $arr_date_in_month);
            }
            $querys->order_by('request_date');
            $res = $querys->get();
        } else {
            $res = null;
        }
        return $res;
    }

    /**
     * get type not approved
     *
     * @param integr  $account_login account_id
     * @return object $querys user request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_type_not_approved_by_user($account_login)
    {
        $list_type = Model_Request::query()
                ->related('request_approval')
                ->related('account')
                ->where('t2.lock', '=', false)
                ->where('t1.account_id', '=', $account_login)
                ->where('t1.status', '=', NOT_APPROVED)
                ->where('t0.approval_status', '=', REQUEST_NEW)
                ->from_cache(false)
                ->group_by('request_id')
                ->get();
        return $list_type;
    }

    /**
     * get user register request
     *
     * @param integer $request_id Request type Id
     * @param integr $user_id User Id
     * @return object $querys user request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_user_register_request_by_type($request_id, $user_id)
    {
        $list_user = Model_Request::query()
                ->related('request_approval')
                ->related('account')
                ->where('t2.lock', '=', false)
                ->where('t1.account_id', '=', $user_id)
                ->where('t1.status', '=', NOT_APPROVED)
                ->where('t0.request_id', '=', $request_id)
                ->where('t0.approval_status', '=', REQUEST_NEW)
                ->from_cache(false)
                ->group_by('account_id')
                ->order_by(array('account.last_name' => 'asc', 'account.first_name' => 'asc'))
                ->get();
        return $list_user;
    }

    /**
     * check user register request in type
     *
     * @param integer $request_id Request type Id
     * @param integer $account_id_login account login
     *
     * @return boolean true|false true: have request, false: not have request
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function is_type_have_user_register($request_id, $account_id_login)
    {
        $user_register = Model_Request::query()
                ->related('request_approval')
                ->related('account')
                ->where('account.lock', '=', false)
                ->where('request_approval.status', '=', NOT_APPROVED)
                ->where('request_approval.account_id', '=', $account_id_login)
                ->where('request_id', '=', $request_id)
                ->where('approval_status', '=', REQUEST_NEW)
                ->from_cache(false)
                ->get();
        return count($user_register) > 0;
    }

    /**
     *  get user register request approved
     *
     * @param integer $request_type_id request type id
     * @param interger $account_id account_id
     * @param int $account_register_request account request
     * @return object  $querys user register request approved
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_user_register_request1($request_type_id, $account_id, $account_register_request)
    {
        $querys = Model_Request::query()
                ->select('t0.account_id', 't0.request_id', 't0.id', 't1.username', array(DB::expr('COUNT(t0.id)'), 'numberrequest'))
                ->related('account')
                ->related('request_approval')
                ->where('t2.account_id', '=', $account_id)
                ->where('t2.status', '=', NOT_APPROVED)
                ->where('t1.lock', '=', false)
                ->where('t0.request_id', '=', $request_type_id)
                ->where('t0.account_id', '=', $account_register_request)
                ->where('t0.approval_status', '=', REQUEST_NEW)
                ->group_by('t0.account_id')
                ->get();
        return $querys;
    }

    /**
     *  get user register request approved
     *
     * @param integer $request_type_id request type id
     * @param interger $account_id account_id
     * @param int $account_register_request account request
     * @param array $arr_request_approved request approved
     * @return object  $querys user request approved
     *
     * @access public
     * @author Nguyen Van Loi
     */
    public static function get_user_register_request2($request_type_id, $account_id, $account_register_request, $arr_request_approved)
    {
        $res = null;
        $querys = Model_Request::query()
                ->select('t0.account_id', 't0.request_id', 't0.id', 't1.username', array(DB::expr('COUNT(t0.id)'), 'numberrequest'))
                ->related('account')
                ->related('request_approval')
                ->where('t2.account_id', '=', $account_id)
                ->where('t2.status', '=', NOT_APPROVED)
                ->where('t1.lock', '=', false)
                ->where('t0.request_id', '=', $request_type_id)
                ->where('t0.account_id', '=', $account_register_request)
                ->where('t0.approval_status', '=', REQUEST_NEW);
        if (count($arr_request_approved) > 0) {
            $querys->where('t0.id', 'in', $arr_request_approved);
            $querys->group_by('t0.account_id');
            $res = $querys->get();
        }
        return $res;
    }

}

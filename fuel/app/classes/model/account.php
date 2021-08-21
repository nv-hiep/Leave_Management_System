<?php

/**
 * /account.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Account
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
class Model_Account extends \Orm\Model
{

    protected static $_table_name = 'account';
    protected static $_properties = array(
        'id',
        'username',
        'fullname',
        'password',
        'company',
        'address',
        'country',
        'city',
        'code',
        'phone',
        'group_id',
        'email',
        'created_at',
        'lang',
        'updated_at',
        'lock'
    );

    /**
     * Relation to account_roles tables
     *
     * @var property of ORM package
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected static $_has_many = array(
        'auth'   => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_AccountRole',
            'key_to'         => 'user_id',
            'cascade_update' => false,
            'cascade_delete' => false
        )
    );

    /**
     * Create validation object
     *
     * @param string $action action (create|edit)
     * @param object $obj object
     * @return object return a validation object
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function validate($action = null, $obj = null)
    {
        $is_admin  = (Auth::check()) ? Model_AccountRole::is_admin(Auth::get_user()->id) : false;


        $val = Validation::forge($action);

        //if register new user or edit normal user => validate user name
        $val->add('name', __('account.name'))
            ->add_rule('required')
            ->add_rule('min_length', 2)
            ->add_rule('max_length', 64)
            ->add_rule('unique_field', 'username', $obj);

        $val->add('fullname', __('account.fullname'))
            ->add_rule('required')
            ->add_rule('min_length', 2)
            ->add_rule('max_length', 64);

        $val->add('company', __('account.company'))
            ->add_rule('min_length', 3)
            ->add_rule('max_length', 64);

        $val->add_field('email', __('account.email'), 'required|valid_email');
        $val->field('email')->add_rule('unique_field', 'email', $obj);

        //if create user -> password required
        if ($action == 'create') {
            $val->add('password', __('account.password'))
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 64)
                ->add_rule('password');
        } elseif ($action == 'edit') {
            $val->add('password', __('account.password'))
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 64)
                ->add_rule('password');
        }

        $val->add_field('address', __('account.address'), 'required|min_length[6]');
        $val->add_field('country', __('account.country'), 'required|min_length[2]');
        $val->add_field('lang', __('account.lang'), 'required|min_length[2]');
        $val->add_field('city', __('account.city'), 'required|min_length[2]');
        $val->add_field('code', __('account.code'), 'required|number|min_length[2]');
        $val->add_field('phone', __('account.phone'), 'required|number|min_length[2]');

        //if admin -> does not validate authority
        if ($obj->id != ADMIN_USER_ID and $is_admin) {
            $val->add('auth', __('common.auth'))
                ->add_rule('auth_selection');
        }

//if admin -> does not validate authority
//        if ($id != ADMIN_USER_ID) {
//            $val->add('authority', __('account.role'))
//                    ->add_rule('auth_selection');
//        }

        return $val;
    }

    /**
     * Create change password validation object
     *
     * @return object return a validation object
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function validate_change_password()
    {
        $val = Validation::forge();
        $val->add('old_password', __('account.old_password'))
                ->add_rule('required')
                ->add_rule('old_password');
        $val->add('new_password', __('account.new_password'))
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 64)
                ->add_rule('new_password')
                ->add_rule('password');
        $val->add('confirm_password', __('account.confirm_password'))
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 64)
                ->add_rule('password')
                ->add_rule('confirm_password');
        return $val;
    }

    /**
     * Create change password validation object
     *
     * @return object return a validation object
     *
     * @access public
     * @author Bui Huu Phuc
     */
    public static function validate_login()
    {
        $val = Validation::forge();
        $val->add('account', __('account.name'))->add_rule('required');
        $val->add('passwd', __('account.password'))->add_rule('required');
        return $val;
    }

    /**
     * Validate csv file
     *
     * @return object return a validation object
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function validate_csv()
    {
        $val = Validation::forge();
        $val->add_field('csv', 'csv', 'required');
        return $val;
    }

    /**
     * Get user from ID
     *
     * @param integer $user_id user ID
     * @return array account
     *
     * @since 1.0
     * @version 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_user_by_id($user_id)
    {
        $user = Model_Account::query()
                ->related('auth')
                ->where('id', $user_id)
                ->get_one();
        $roles = array();
        foreach ($user->auth as $role) {
            $roles[] = $role->role_id;
        }
        $user->auth = $roles;
        return $user;
    }

    /**
     * Get Accounts' info
     *
     * @param integer $user_id user ID
     * @return array account
     *
     * @since 1.0
     * @version 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function get_all_accounts()
    {
        $user = Model_Account::query()
              ->get();
        return $user;
    }

    /**
     * Get all language
     *
     * @return return
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Bui Huu Phuc
     */
    public static function get_all_language()
    {
        $countries = parse_ini_file(USER_CONFIG_PATH . '/country_language.ini', true);
        $ret = array();
        foreach ($countries as $key => $country) {
            $ret[$key] = $country['language_name'];
        }
        return $ret;
    }

    /**
     * get Primary group
     *
     * @return primary group name
     *
     * @access public
     * @author Bui Huu Phuc
     *
     */
    public function get_primary_group()
    {
        //get primary group id
        $primary_group = Model_Group::query()
                ->where('account_id', $this->id)
                ->where('primary_group', true)
                ->get_one();

        if (empty($primary_group)) {
            $this->primary_group_name = '';
            return;
        }

        //get primary group name
        $group_info = Model_Mstgroup::query()
                ->where('id', $primary_group->group_id)
                ->get_one();

        $this->primary_group_name = $group_info->group_name;
    }

    /**
     * get Authority name
     *
     * @return authority name (Comma-separated)
     *
     * @access public
     * @author Bui Huu Phuc
     *
     */
    public function get_authority()
    {
        //get authority id
        $authorities = Model_Authority::get_authority($this->id);
        $ret = array();
        foreach ($authorities as $au) {
            $role = Model_Role::find($au);
            $ret[] = __('account.' . $role->name);
        }

        $this->authority_name = implode(', ', $ret);
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
        $accounts = Model_Account::query()
                ->related('request_approval_route')
                ->where('request_approval_route.request_id', '=', $request_id)
                ->where('lock', '=', false)
                ->order_by('first_name', 'ASC')
                ->get();
        return $accounts;
    }

}

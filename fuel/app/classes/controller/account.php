<?php

/**
 * /account.php
 *
 * @copyright Copyright (C) X -TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 6, 2014
 * @version $Id$
 * @license X -TRANS Develop License 1.0
 */

/**
 * Account
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 6, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Controller_Account extends Controller_Base
{

    /**
     * Display all registered accounts
     *
     * @access public
     * @author Nguyen Van Hiep
     *
     * @version 1.0
     * @since 1.0
     */
    public function action_index()
    {
        if (!$this->user or $this->user->id != ADMIN_USER_ID) {
            Response::redirect('home');
        }
        $view        = View::forge('account/index');
        $view->users = Model_Account::get_all_accounts();

        $this->template->title   = 'Accounts';
        $this->template->content = $view;
    }

    /**
     * Create Authority
     *
     * @param integer $account_id account id
     * @param boolean $edit remove old groups when edit
     * @return void
     *
     * @access protected
     * @since 1.0
     * @version 1.0
     * @author Bui Huu Phuc
     */
    protected function create_authority($account_id, $edit = false)
    {
        if ($edit) {
            //remove all account authority
            Model_Authority::query()->where('user_id', $account_id)->delete();
        }
        $auth = !empty(Input::post('auth')) ? Input::post('auth') : array(USER_AUTH);
        //save authority
        foreach ($auth as $au) {
            Model_Authority::forge(array(
                'user_id' => $account_id,
                'role_id' => $au
            ))->save();
        }

        //delete author cache
        Cache::delete(\Config::get('ormauth.cache_prefix', 'auth') . '.permissions.user_' . $account_id);
    }

    /**
     * Register account
     *
     * @params integer $back back to account or user-info
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Bui Huu Phuc
     */
    public function action_register($back = 0)
    {
        $view            = View::forge('account/register');
        $view->err       = array();
        $view->is_admin  = isset($this->user) ? Model_AccountRole::is_admin($this->user->id) : false;
        $rdr             = ($back === '0') ? 'account' : 'user/info';
        $rdr             = isset($this->user) ? $rdr : 'account/register';
        $view->rdr       = $rdr;
        $view->languages = $this->get_languages('select');
        $view->countries = $this->get_countries();

        if (Input::method() == 'POST') {
            $account             = Model_Account::forge();
            $account->username   = Input::post('name');
            $account->fullname   = Input::post('fullname');
            $account->password   = Auth::hash_password(Input::post('password'));
            $account->lang       = Input::post('lang');
            $account->company    = Input::post('company');
            $account->email      = Input::post('email');
            $account->address    = Input::post('address');
            $account->country    = Input::post('country');
            $account->city       = Input::post('city');
            $account->code       = Input::post('code');
            $account->phone      = Input::post('phone');
            $account->group_id   = Input::post('group') ? Input::post('group') : USER_GROUP;
            $account->lock       = Input::post('lock')  ? Input::post('lock')  : false;
            $account->created_at = date('Y-m-d H:i:s', time());
            $account->updated_at = date('Y-m-d H:i:s', time());

            $val = Model_Account::validate('create', $account);
            if ($val->run()) {
                //save account
                if ($account->save()) {
                    //save authority
                    $this->create_authority($account->id);
                    //redirect to index page
                    Session::set_flash('success', __('message.account_:username_registered', array('username' => $account->username)));
                    Response::redirect($rdr);
                } else { //fail in transaction
                    Session::set_flash('error', __('message.registration_failed'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->err = $val->error_message();
            }
        }

        $view->auths             = Model_Role::get_all_roles();
        $this->template->title   = 'Register new account';
        $this->template->content = $view;
    }

    /**
     * Action edit info
     *
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    public function action_edit($id = null)
    {
        if (!$this->user) {
            Response::redirect('home/index');
        }

        if (!is_null($id)) {
            $account = Model_Account::get_user_by_id($id);
            if (!$account) {
                Session::set_flash('error', __('message.account_does_not_exist'));
                Response::redirect('account');
                exit();
            }

            //administrator can not be edited by other users
            if ($id == ADMIN_USER_ID && $this->user->id != ADMIN_USER_ID) {
                Response::redirect("/base/error");
                exit();
            }
            $rdr = 'account';
        } else {
            $account = Model_Account::get_user_by_id($this->user->id);
            $rdr     = 'user/info';
        }

        $view           = View::forge('account/edit');
        $view->err      = array();
        $view->account  = $account;
        $view->rdr      = $rdr;
        $view->is_admin = Model_AccountRole::is_admin($this->user->id);

        $view->edit_me    = ($id == $this->user->id);
        $view->edit_admin = ($id == ADMIN_USER_ID);

        if (Input::method() == 'POST') {
            $account->username   = Input::post('name');
            $account->fullname   = Input::post('fullname');
            $account->lang       = Input::post('lang');
            $account->company    = Input::post('company');
            $account->email      = Input::post('email');
            $account->address    = Input::post('address');
            $account->country    = Input::post('country');
            $account->city       = Input::post('city');
            $account->code       = Input::post('code');
            $account->phone      = Input::post('phone');
            $account->group_id   = Input::post('group') ? Input::post('group') : USER_GROUP;
            $account->lock       = Input::post('lock')  ? true  : false;
            $account->updated_at = date('Y-m-d H:i:s', time());

            $val = Model_Account::validate('user_edit', $account);
            if ($val->run()) {
                $account->auth = null;
                //save account
                if ($account->save()) {
                    //check is edit admin
                    if (!$view->edit_admin) {
                        $this->create_authority($account->id, true);
                    }
                    //redirect to index page
                    Session::set_flash('success', __('message.account_:username_edited', array('username' => $account->username)));
                    Response::redirect($rdr);
                } else { //fail in transaction
                    Session::set_flash('error', __('message.registration_failed'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->err = $val->error_message();
            }
        }
        $view->countries         = $this->get_countries();
        $view->languages         = $this->get_languages();
        $view->auths             = Model_Role::get_all_roles();
        $this->template->title   = __('account.edit');
        $this->template->content = $view;
    }

    /**
     * Delete account
     *
     * @param integer $id account ID
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public function action_delete($id = null)
    {
        $account = Model_Account::get_user_by_id($id);

        if (!$account) {
            Session::set_flash('error', __('message.account_does_not_exist'));
            Response::redirect('account');
        }
        if ($id == 1) {
            Session::set_flash('error', __('message.cannot_del_account'));
            Response::redirect('account');
        }

        if ($account->delete()) {
            Session::set_flash('success', __('message.deleted_account'));
            Response::redirect('account');
        } else {
            Session::set_flash('error', __('message.cannot_del_account'));
            Response::redirect('account');
        }
    }
}

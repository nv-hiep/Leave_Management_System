<?php

/**
 * /user.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * User
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
class Controller_User extends Controller_Base
{

    /**
     * Action login
     *
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Bui Huu Phuc
     */
    public function action_login()
    {
        // if logged in -> go to home page corresponding with authority of user
        if (Auth::check()) {
            Response::redirect('home/index');
        }

        //prepare view
        $view      = View::forge('common/login');
        $view->err = array();

        if (Input::method() == "POST") {
            $val = Model_Account::validate_login();
            if ($val->run()) {
                //get user
                $account = Model_Account::query()
                        ->select('id')
                        ->where('username', Input::post('account'))
                        ->where('lock', false)
                        ->get_one();

                if (!$account) {
                    Session::set_flash('error', __('message.account_or_password_not_correct'));
                } else {
                    //check authority
//                    $autorities = Model_Authority::get_authority($account->id);
//                    if (in_array(REQUEST_AUTHORITY, $autorities) || in_array(MANAGER_AUTHORITY, $autorities)) {
//                        if (Auth::login()) {
//                            Session::set('login_port', USER_PORT);
//                            Response::redirect('/user/index');
//                        } else {
//                            Session::set_flash('error', __('message.login_error'));
//                        }
//                    } else {
//                        Session::set_flash('error', __('message.login_error'));
//                    }

                    if (Auth::login()) {
                        Response::redirect('home/index');
                    } else {
                        Session::set_flash('error', __('message.account_or_password_not_correct'));
                    }
                }
            } else {
                Session::set_flash('error', __('message.login_error'));
                $view->err = $val->error_message();
            }
        }
        $this->template->title   = 'Log In';
        $this->template->content = $view;
    }

    /**
     * Action index
     *
     * @return void
     *
     * @since 1.0
     * @version 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public function action_index()
    {
        $view                    = View::forge('user/index');
        $this->template->title   = 'Home';
        $this->template->content = $view;
    }

    /**
     * Info of account
     *
     * @return void
     *
     * @since 1.0
     * @version 1.0
     * @access public
     * @author Nguyen Van Hiep
     */
    public function action_info()
    {
        if (!$this->user) {
            Response::redirect('home/index');
        }
        $view                    = View::forge('user/info');
        $view->account           = Model_Account::get_user_by_id($this->user->id);
        $view->countries         = $this->get_countries();
        $view->languages         = $this->get_languages();
        $this->template->title   = $this->user->username;
        $this->template->content = $view;
    }

    /**
     * Action change password
     *
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    public function action_password()
    {
        if (!$this->user) {
            Response::redirect('home/index');
        }

        $view      = View::forge('common/change_password');
        $view->err = array();

        if (Input::method() == 'POST') {
            $val = Model_Account::validate_change_password();
            if ($val->run()) {

                //change password
                if (Auth::change_password(Input::post('old_password'), Input::post('new_password'), Auth::get('username'))) {
                    Session::set_flash('success', __('message.password_changed'));
                } else {
                    Session::set_flash('error', __('message.registration_failed'));
                }
            } else { //validate error
                Session::set_flash('error', __('message.password_changed_unsuccessfully'));
                $view->err = $val->error_message();
            }
        }
        $this->template->title   = __('common.change_password');
        $this->template->content = $view;
    }
}
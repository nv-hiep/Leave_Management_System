<?php

/**
 * /roles.php
 *
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 6, 2014
 * @version $Id$
 * @license X -TRANS Develop License 1.0
 */
class Controller_Admin_Roles extends Controller_Admin
{

    /**
     * Display all registered roles
     *
     * @access public
     * @author Nguyen Van Hiep
     *
     * @version 1.0
     * @since 1.0
     */
    public function action_index()
    {
        $view        = View::forge('admin/roles/index');
        $view->auths = Model_Role::get_all_roles();

        $this->template->title   = 'Roles';
        $this->template->content = $view;
    }

    /**
     * Add Authority
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function action_register()
    {
        $view        = View::forge('admin/roles/register');
        $view->error = array();

        if (Input::method() == 'POST') {
            $auth       = Model_Role::forge();
            $auth->name = Input::post('auth');
            $val        = Model_Role::validate('add', $auth);
            if ($val->run()) {
                if ($auth->save()) {
                    Session::set_flash('success', __('message.auth_:name_added', array('name' => $auth->name)));
                    Response::redirect('admin/roles/');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.cannot_add_auth'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->error = $val->error_message();
            }
        }

        $this->template->title   = 'Add Authority';
        $this->template->content = $view;
    }

    /**
     * Edit Authority
     *
     * @param void
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function action_edit($id = null)
    {
        $auth = Model_Role::find($id);
        if (!$auth) {
            Session::set_flash('error', __('message.auth_not_exists'));
            Response::redirect('admin/roles');
        }

        if ($auth and ( $id <= 2)) {
            Session::set_flash('error', __('message.cannot_edit_auth'));
            Response::redirect('admin/roles');
        }

        $view        = View::forge('admin/roles/edit');
        $view->auth  = $auth;
        $view->error = array();

        if (Input::method() == 'POST') {
            $val = Model_Role::validate('edit', $auth);
            if ($val->run()) {
                $auth->name = Input::post('auth');
                if ($auth->save()) {
                    Session::set_flash('success', __('message.edited_auth'));
                    Response::redirect('admin/roles');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.cannot_edit_auth'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->error = $val->error_message();
            }
        }
        $this->template->title   = 'Edit Authority';
        $this->template->content = $view;
    }

    /**
     * Delete Authority
     *
     * @param void
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function action_delete($id = null)
    {
        $auth = Model_Role::find($id);
        if (!$auth) {
            Session::set_flash('error', __('message.auth_not_exists'));
            Response::redirect('admin/roles');
        }

        $auth_used = Model_Role::is_auth_used($id);
        if (($id <= 2) or ( $auth_used)) {
            Session::set_flash('error', __('message.cannot_del_auth'));
            Response::redirect('admin/roles');
        }

        if ($auth->delete()) {
            Session::set_flash('success', __('message.auth_deleted'));
            Response::redirect('admin/roles');
        } else {
            Session::set_flash('error', __('message.cannot_del_auth'));
        }
    }
}
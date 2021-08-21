<?php

/**
 * /roleperm.php
 *
 * @copyright Copyright (C) X -TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 6, 2014
 * @version $Id$
 * @license X -TRANS Develop License 1.0
 */

/**
 * Admin_Roleperm
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
class Controller_Admin_Roleperm extends Controller_Admin
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
        $view      = View::forge('admin/roleperm/index');
        $rps       = Model_Roleperms::get_all_roleperms();
        $view->rps = $rps;

        $this->template->title   = 'Role - Permission';
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
        $view        = View::forge('admin/roleperm/register');
        $view->error = array();

        $contr_info        = $this->get_controller();
        $controllers       = $contr_info['controllers'];
        $permissions       = $contr_info['names'];
        $view->controllers = $controllers;
        $view->perms       = $permissions;
        $perms             = Model_AccountPerms::get_list_perms();

        if (Input::method() == 'POST') {
            $rp           = Model_Roleperms::forge();
            $rp->role_id  = Input::post('role');
            $rp->perms_id = array_search(Input::post('perm'), $perms);
            $rp->actions  = (Input::post('act')) ? serialize(array_map('intval', Input::post('act'))) : '';
            $val          = Model_Roleperms::validate('add');
            if ($val->run()) {
                if ($rp->save()) {
                    Session::set_flash('cache', 'del');
                    Session::set_flash('success', __('message.set_role_perm'));
                    Response::redirect('admin/roleperm/');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.cannot_set_role_perm'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->error = $val->error_message();
            }
        }

        $view->auths             = Model_Role::get_all_roles(true);
        $this->template->title   = 'Add Role Permission';
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
        $rp = Model_Roleperms::get_roleperm($id);
        if (!$rp) {
            Session::set_flash('error', __('message.rp_not_exists'));
            Response::redirect('admin/roleperm');
        }

        $view        = View::forge('admin/roleperm/edit');
        //$rp->actions = array_map('intval', unserialize($rp->actions));
        $view->rp    = $rp;
        $view->error = array();

        $contr_info        = $this->get_controller();
        $controllers       = $contr_info['controllers'];
        $permissions       = $contr_info['names'];
        $view->controllers = $controllers;
        $view->perms       = $permissions;
        $perms             = Model_AccountPerms::get_list_perms();

        if (Input::method() == 'POST') {
            $rp->role_id  = Input::post('role');
            $rp->perms_id = array_search(Input::post('perm'), $perms);
            $rp->actions  = (Input::post('act')) ? serialize(array_map('intval', Input::post('act'))) : '';
            $val          = Model_Roleperms::validate('edit');
            if ($val->run()) {
                if ($rp->save()) {
                    Session::set_flash('cache', 'del');
                    Session::set_flash('success', __('message.set_role_perm'));
                    Response::redirect('admin/roleperm');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.cannot_set_role_perm'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->error = $val->error_message();
            }
        }

        $view->auths             = Model_Role::get_all_roles(true);
        $this->template->title   = 'Edit Role-Permission';
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
        $rp = Model_Roleperms::find($id);
        if (!$rp) {
            Session::set_flash('error', __('message.rp_not_exists'));
            Response::redirect('admin/roleperm');
        }

        if ($rp->delete()) {
            Session::set_flash('cache', 'del');
            Session::set_flash('success', __('message.rp_deleted'));
            Response::redirect('admin/roleperm');
        } else {
            Session::set_flash('error', __('message.cannot_del_rp'));
        }
    }

    /**
     * Ajax Load actions of a permission
     *
     * @param void
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function action_ajaxload_actions()
    {
        $perm     = Input::post('perm');
        $pid      = Input::post('pid');
        $rid      = Input::post('rid');
        $acts     = (strlen(Input::post('acts')) > 0) ? array_map('to_number', unserialize(Input::post('acts'))) : array();
        $num_only = $this->is_only_int($acts);

        $ret = '';
        if (empty($pid)) {
            return false;
        }

        if (strlen($perm) == 0) {
            return false;
        }

        $contr_info  = $this->get_controller();
        $controllers = $contr_info['controllers'];
        $actions     = $controllers[$perm];

        foreach ($actions as $key => $action) {
            if ($num_only) {
                $checked = (in_array($key, $acts)) ? 'checked' : '';
            } else {
                $checked = (in_array($action, $acts)) ? 'checked' : '';
            }
            $ret .= "<label>
                        <input name='act[]' value={$key} type='checkbox' {$checked} class='checkbox-act'>{$action}
                    </label>
                    <br>";
        }
        $ret = $ret . '<br>';
        return $ret;
    }

    /**
     * Role - Permission actions
     *
     * @param string Role - Permission actions
     *
     * @author Nguyen Van Hiep
     * @access protected
     */
    protected function rpactions()
    {
        $i     = 0;
        $n     = count(Input::post('act'));
        $final = 'a:' . $n . ':{';

        foreach (Input::post('act') as $val) {
            $final .= 'i:' . $i . ';i:' . $val . ';';
            $i++;
        }
        $final .= '}';

        return $final;
    }

    /**
     * Check if an array only contains interger
     *
     * @param array array to check
     * @param boolean
     *
     * @author Nguyen Van Hiep
     * @access protected
     */
    protected function is_only_int($array)
    {
        foreach ($array as $value) {
            if (!is_int($value)) {
                return false;
            }
        }
        return true;
    }

}

/**
 * Convert string to number
 *
 * @param string number under format of string
 * @return integer | string
 *
 * @author Nguyen Van Hiep
 * @access protected
 */
function to_number($str)
{
    if (is_numeric($str)) {
        return (int) $str;
    }
    return $str;
}

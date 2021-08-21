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
class Controller_Admin_Perms extends Controller_Admin
{

    /**
     * Display
     *
     * @param integer
     *
     * @access public
     * @author
     *
     * @version 1.0
     * @since 1.0
     */
    public function action_index()
    {
        $view                    = View::forge('admin/perms/index');
        $view->perms             = Model_AccountPerms::get_all_perms();
        $this->template->title   = 'Permissions';
        $this->template->content = $view;
    }

    /**
     * Add permission
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function action_register()
    {
        $view        = View::forge('admin/perms/register');
        $view->error = array();

        $contr_info        = $this->get_controller();
        $controllers       = $contr_info['controllers'];
        $permissions       = $contr_info['names'];
        $view->controllers = $controllers;
        $view->perms       = $permissions;

        if (Input::method() == 'POST') {
            $perm              = Model_AccountPerms::forge();
            $perm->area        = Input::post('area');
            $perm->permission  = Input::post('perm');
            $perm->description = Input::post('desc');
            $perm->actions     = (Input::post('perm')) ? self::serialise($controllers[Input::post('perm')]) : array();
            $perm->user_id     = 0;
            $perm->created_at  = date('Y-m-d h:i:s', time());
            $perm->updated_at  = date('Y-m-d h:i:s', time());
            $val = Model_AccountPerms::validate('add', $perm);
            if ($val->run()) {
                if ($perm->save()) {
                    Session::set_flash('cache', 'del');
                    Session::set_flash('success', __('message.perm_added'));
                    Response::redirect('admin/perms/');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.cannot_add_perm'));
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
     * Edit permission
     *
     * @param void
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function action_edit($id = null)
    {
        $perm = Model_AccountPerms::find($id);
        if (!$perm) {
            Session::set_flash('error', __('message.perm_not_exists'));
            Response::redirect('admin/perms');
        }

        $view          = View::forge('admin/perms/edit');
        //$perm->actions = unserialize($perm->actions);
        $view->perm    = $perm;
        $view->error   = array();

        $contr_info        = $this->get_controller();
        $controllers       = $contr_info['controllers'];
        $permissions       = $contr_info['names'];
        $view->controllers = $controllers;
        $view->perms       = $permissions;

        if (Input::method() == 'POST') {
            $perm->area        = Input::post('area');
            $perm->permission  = Input::post('perm');
            $perm->description = Input::post('desc');
            $perm->actions     = (Input::post('perm')) ? self::serialise($controllers[Input::post('perm')]) : array();
            $perm->user_id     = 0;
            $perm->updated_at  = date('Y-m-d h:i:s', time());
            $val = Model_AccountPerms::validate('edit', $perm);
            if ($val->run()) {
                if ($perm->save()) {
                    Session::set_flash('cache', 'del');
                    Session::set_flash('success', __('message.perm_edited'));
                    Response::redirect('admin/perms');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.cannot_edit_perm'));
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
     * Add permission
     *
     * @param $id string delete
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function action_delete($id = null)
    {
        $perm = Model_AccountPerms::find($id);
        if (!$perm) {
            Session::set_flash('error', __('message.perm_not_exists'));
            Response::redirect('admin/perms');
        }

        if ($perm->delete()) {
            Model_AccountPerms::del_roleperm_by_perm_id($id);
            Session::set_flash('cache', 'del');
            Session::set_flash('success', __('message.perm_deleted'));
            Response::redirect('admin/perms');
        } else {
            Session::set_flash('error', __('message.cannot_del_perm'));
        }
    }

    /**
     * Serialize
     *
     * @param array $actions actions of controller
     * @return string serialized-actions
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public static function serialise($actions)
    {
        $acts = (Input::post('act')) ? : array();

        $checked = array();
        foreach ($acts as $act) {
            if (array_key_exists($act, $actions)) {
                $checked[] = $actions[$act];
            }
        }

        $ret = serialize($checked);

        return $ret;
    }

    /**
     * Check if area-permission pair exists
     *
     * @return boolean
     *
     * @author Nguyen Van Hiep
     * @access protected
     */
    protected function perm_field_exists()
    {
        $perms = Model_AccountPerms::get_all_perms();
        foreach ($perms as $perm) {
            if (($perm->area == Input::post('area')) and ($perm->permission == Input::post('perm'))) {
                return true;
            }
        }

        return false;
    }

}

<?php

/**
 * /currency.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 14, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Language
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
class Controller_Currency extends Controller_Base
{

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
        $view        = View::forge('currency/index');
        $view->currs = Model_Currency::get_currencies();

        $this->template->title   = 'Currency';
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
    public function action_add()
    {
        $view      = View::forge('currency/add');
        $view->err = array();

        if (Input::method() == 'POST') {
            $curr             = Model_Currency::forge();
            $curr->name       = Input::post('name');
            $curr->code       = Input::post('code');
            $curr->symbol     = Input::post('symbol');
            $curr->rate       = Input::post('rate');
            $curr->created_at = date('Y-m-d H:i:s', time());
            $curr->updated_at = date('Y-m-d H:i:s', time());

            $val = Model_Currency::validate('create', $curr);
            if ($val->run()) {
                //save account
                if ($curr->save()) {
                    Session::set_flash('success', __('message.curr_:username_registered', array('name' => $curr->name)));
                    Response::redirect('currency');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.registration_failed'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->err = $val->error_message();
            }
        }

        $this->template->title   = 'Add new Currency';
        $this->template->content = $view;
    }

    /**
     * Action edit info
     *
     * @param integer $id currency ID
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    public function action_edit($id = null)
    {
        $curr = Model_Currency::get_currency_by_id($id);
        if (!$curr) {
            //redirect to index page
            Session::set_flash('warning', __('message.curr_not_exist'));
            Response::redirect('currency');
            exit();
        }
        $view      = View::forge('currency/edit');
        $view->err = array();

        if (Input::method() == 'POST') {
            $curr->name       = Input::post('name');
            $curr->code       = Input::post('code');
            $curr->symbol     = Input::post('symbol');
            $curr->rate       = Input::post('rate');
            $curr->updated_at = date('Y-m-d H:i:s', time());

            $val = Model_Currency::validate('edit', $curr);
            if ($val->run()) {
                //save account
                if ($curr->save()) {
                    //redirect to index page
                    Session::set_flash('success', __('message.curr_:name_edited', array('name' => $curr->name)));
                    Response::redirect('currency');
                } else { //fail in transaction
                    Session::set_flash('error', __('message.registration_failed'));
                }
            } else {//validate error
                Session::set_flash('error', __('message.validation_error'));
                $view->err = $val->error_message();
            }
        }

        $view->curr = $curr;
        $this->template->title   = 'Edit currency';
        $this->template->content = $view;
    }

    /**
     * Delete currency
     *
     * @param integer $id currency ID
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    public function action_delete($id = null)
    {
        $curr = Model_Currency::get_currency_by_id($id);
        if (!$curr) {
            //redirect to index page
            Session::set_flash('warning', __('message.curr_not_exist'));
            Response::redirect('currency');
            exit();
        }

        if ($curr->delete()) {
            Session::set_flash('success', __('message.curr_deleted'));
            Response::redirect('currency');
        } else {
            Session::set_flash('error', __('message.cannot_delete_curr'));
            Response::redirect('currency');
        }
    }
}

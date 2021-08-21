<?php

/**
 * /admin.php
 *
 * @copyright Copyright (C) 2015 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package smarty
 * @since May 20, 2015
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Admin
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 6, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Controller_Admin extends Controller_Base
{

    /**
     * Index
     *
     * @access public
     * @version 1.0
     * @since 1.0
     * @author Nguyen Van Hiep
     */
    public function action_index()
    {
        $this->template->title   = 'Super Administrator';
        $this->template->content = View::forge('admin/index');
    }
}
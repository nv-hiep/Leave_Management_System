<?php

/**
 * /home.php
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
class Controller_Home extends Controller_Base
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
        $view                    = View::forge('user/index');
        $this->template->title   = 'Home';
        $this->template->content = $view;
    }

}

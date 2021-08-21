<?php

/**
 * /counter.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Nguyen Van Hiep
 * @package tmd
 * @since Nov 24, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */

/**
 * Counter
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
class Model_Counter extends \Orm\Model
{

    protected static $_table_name ='counter';
    protected static $_primary_key = array('account_id');

    protected static $_properties = array(
        'account_id',
        'day_off',
        'vacation',
        'overtime_work',
    );
}
<?php

/**
 * /const.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Bui Huu Phuc
 * @package tmd
 * @since Nov 19, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
define('ADMIN_AUTH',         1);
define('USER_AUTH',          2);

//admin user
define('ADMIN_USER_ID', 1);
define('USER_GROUP'   , 1);

define('APPROVER_PORT', 'approver');
define('USER_PORT',     'user');
define('COMMON_AREA',   'som');

//log file directory
define('LOG_FILE_DIR', '/var/log');

// calculation
define('ADDITION',      '1');
define('SUBTRACTION',   '0');
define('NOT_CALCULATE', null);
define('DAY_ROUND',     2);
define('WORKING_TIME',  8);
define('HALFDAY_OFF',   4);
define('FULLDAY_OFF',   8);
define('NONE',          0);

//csv download
define('CONFIRM_CSV_FILE_NAME', 'User_Internal_Counter_');
define('REQUEST_APPROVAL_CSV',  'Request_Approval_');

// display calendar
define('SUNDAY',                0);
define('SATURDAY',              6);
define('START_WEEK_IN_MONTH',   1);
define('NUMBER_OF_DAY_IN_WEEK', 7);

// Default opening time and closing time
define('OPENING_TIME',    '09:00');
define('CLOSING_TIME',    '18:00');

// Date select type
define('ALL_DATES',             '0');
define('WORKING_DATE_ONLY',     '1');
define('HOLIDAY_ONLY',          '2');

// Date select type
define('CHECKBOX',              0);
define('SHIFT_CHECKBOX',        1);

// Date pattern YYYY/MM/DD
define('DATE_PATTERN',       '/^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}/');
define('DEFAULT_LANG',       'en');
define('FIRST_WEEK_IN_MONTH', 1);
define('USER_CONFIG_PATH',   'C:\xampp\htdocs\tmd\config');
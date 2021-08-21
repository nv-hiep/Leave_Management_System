<?php

/**
 * /mainholiday.php
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 20, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
use Orm\Model;
/**
 * nationalholiday
 *
 * <pre>
 * </pre>
 *
 * @copyright Copyright (C) 2014 X-TRANS inc.
 * @author Dao Anh Minh
 * @package tmd
 * @since Nov 20, 2014
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
class Model_Nationalholiday extends Model
{
    protected static $_table_name = 'national_holiday';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'date',
        'country'
    );

    /**
     * get all holiday in specified month include holiday of specified country and addition custom holiday
     *
     * @param integer $user_id user id
     * @param date $month
     * @return mixed
     *
     * @access public
     * @author Dao Anh Minh
     */
    public static function get_all_holidays_in_month($user_id, $month)
    {
        $next_month  =  date("Y/m",strtotime ("+1 month", strtotime($month.'/01')));
        $prev_month  =  date("Y/m",strtotime ("-1 month", strtotime($month.'/01')));

        // get group ids which user belong to
        $groups = Model_Group::get_active_group_by_user($user_id);

        // get abbreivation of the countries [ja,vn]
        $abbr_countries = Model_Mstgroup::get_country_by_group($groups);

        // get addition custom holiday of groups
        $group_holidays = Model_Mstholiday::query()
                ->related('holidays')
                ->where('holidays.group_id', 'IN', $groups)
                ->and_where_open()
                    ->or_where(DB::expr('DATE_FORMAT(holiday,"%Y/%m")'), $month)
                    ->or_where(DB::expr('DATE_FORMAT(holiday,"%Y/%m")'), $prev_month)
                    ->or_where(DB::expr('DATE_FORMAT(holiday,"%Y/%m")'), $next_month)
                ->and_where_close()
                ->get();

        // get holiday based on country of groups
        $national_holidays = Model_Nationalholiday::query()
                ->where('country', 'IN', $abbr_countries)
                ->and_where_open()
                    ->or_where(DB::expr('DATE_FORMAT(date,"%Y/%m")'), $month)
                    ->or_where(DB::expr('DATE_FORMAT(date,"%Y/%m")'), $prev_month)
                    ->or_where(DB::expr('DATE_FORMAT(date,"%Y/%m")'), $next_month)
                ->and_where_close()
                ->get();

        $ret_main_holiday = array();
        foreach ($national_holidays as $main_value) {
            $ret_main_holiday[date('Y/m/d', strtotime($main_value->date))] = date('Y/m/d', strtotime($main_value->date));
        }

        foreach ($group_holidays as $spe_value) {
            if (!key_exists(date('Y/m/d', strtotime($spe_value->holiday)), $ret_main_holiday)) {
                $ret_main_holiday[date('Y/m/d', strtotime($spe_value->holiday))] = date('Y/m/d', strtotime($spe_value->holiday));
            }
        }

        return $ret_main_holiday;
    }

    /**
     * get google api key and google calendar id of the country
     *
     * @return array information about google calendar
     *
     * @access public
     * @author Dao Anh Minh
     *
     * @version 1.0
     * @since 1.0
     */
    public static function get_google_calendar_info()
    {
        $file_path = USER_CONFIG_PATH.'/calendar.ini';
        $google_calendar = array();

        // if calendar file does not exist -> default country is Japan
        if (!File::exists($file_path)) {
            return $google_calendar;
        }

        // if information about google calendar exists -> get google calendar's info
        $google_calendar = parse_ini_file(USER_CONFIG_PATH.'/calendar.ini', true);

        return $google_calendar;
    }
}

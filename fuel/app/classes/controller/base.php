<?php

/**
 * Base class extended from other class
 *
 * @author Dao Anh Minh
 * @package localsite
 * @version $Id$
 * @license X-TRANS Develop License 1.0
 */
use Fuel\Core\Controller_Template;
use Fuel\Core\View;
use Fuel\Core\Lang;
use Fuel\Core\Asset;
use Auth\Auth;

class Controller_Base extends Controller_Template
{

    public $template = 'template';
    public $user     = null;
    public $contr    = array();

    /**
     * Run before display a page
     *
     * @access public
     * @author Dao Anh Minh
     * @author Bui Huu Phuc
     */
    public function before()
    {
        $this->check_permission();
        // load language file
        $user = Model_Account::find(Auth::get('id'));
        $lang = isset($user->lang) ? $user->lang : DEFAULT_LANG;
        Config::set('language', $lang);
        $arr_lang = Lang::load('language.ini');

        //before
        parent::before();

        // load base css
        $this->baseCss();
        // load base js
        $this->baseJs();

        //assign variables to template
        $this->template->set('language', json_encode($arr_lang['javascript']), false);
        $this->template->set('sys_lang', $this->get_languages('menu'));
        $this->template->set('sys_curr', $this->get_currencies());

        if (Auth::check()) {
            $this->user = Auth::get_user();
            $this->template->set_global('login_user', Auth::get_user());
        }
    }

    /**
     * display error 404 not page found
     *
     * @access public
     * @author Dao Anh Minh
     */
    public function action_404()
    {
        $view                    = View::forge('common/404');
        $this->template->title   = 'Page not found';
        $this->template->content = $view;
    }

    /**
     * display access denied page
     *
     * @access public
     * @author Dao Anh Minh
     */
    public function action_error()
    {
        $view                    = View::forge('common/error');
        $this->template->title   = 'Access denied';
        $this->template->content = $view;
    }

    /**
     * Action logout
     *
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Bui Huu Phuc
     */
    public function action_logout()
    {
        Auth::logout();
//        $login_port = Session::get('login_port');
//        Session::delete('login_port');
        Session::destroy();
        Response::redirect("home/index");
        exit();
    }

    /**
     * load base css
     *
     * @access public
     * @author Dao Anh Minh
     */
    public function baseCss()
    {
        $this->addCss('style.css');
        $this->addCss('form.css');
        $this->addCss('exo2.css');
        $this->addCss('megamenu.css');
        $this->addCss('fwslider.css');
        $this->addCss('som.css');
    }

    /**
     * load base js
     *
     * @access public
     * @author Dao Anh Minh
     */
    public function baseJs()
    {
        $this->addJs('jquery1.min.js');
        $this->addJs('megamenu.js');
        $this->addJs('megamenu_startup.js');
        $this->addJs('jquery-ui.min.js');
        $this->addJs('css3-mediaqueries.js');
        $this->addJs('fwslider.js');
        $this->addJs('jquery.blockUI.js');
        $this->addJs('jquery.easydropdown.js');
        $this->addJs('function.js');
    }

    /**
     * add more neccessary css file to use
     *
     * @param string $file name of css file
     *
     * @access public
     * @author Dao Anh Minh
     */
    public function addCss($file)
    {
        Asset::css($file, array(), 'css', false);
    }

    /**
     * add more neccessary js file to use
     *
     * @param string $file name of js file
     *
     * @access public
     * @author Dao Anh Minh
     */
    public function addJs($file)
    {
        Asset::js($file, array(), 'js', false);
    }

    /**
     * check permission
     *
     * @return mix
     *
     * @access public
     * @author Bui Huu Phuc
     */
    protected function check_permission()
    {
        //get controller name and action name
        $controller = strtolower(substr(Request::active()->controller, 11));
        $action     = Request::active()->action;

        //get login port
        $perm = Model_AccountPerms::get_perm_info($controller);
        $area = $perm->area;

        //allow login and error page
        if ($action == 'login' || $action == 'error') {
            return true;
        }

        //check login
//        if (!Auth::check()) {
//            if (Input::is_ajax()) {
//                echo 'not_logged_in';
//                exit();
//            }
//            Response::redirect("/user/index");
//            exit();
//        }

        if (!Auth::check() and $area != 'user') {
            Response::redirect("home");
            exit();
        }

        //check user lock
        if (Auth::check()) {
            $account = Model_Account::find(Auth::get('id'));
            if ($account->lock) {
                Auth::logout();
                Response::redirect("/user/index");
                exit();
            }
        }

        //access base controller
//        if ($controller == 'base') {
//            $area = COMMON_AREA;
//        }

        //check authority
        if (Auth::check() and !Auth::has_access("{$area}.{$controller}[{$action}]")) {
            Response::redirect("/base/error");
            exit();
        }
    }

    /**
     * Get all temporary of time-leave based on request type of user
     *
     * @return array currencies
     *
     * @since 1.0
     * @version 1.0
     * @access protected
     * @author Dao Anh Minh
     * @author Nguyen Van Hiep
     */
    protected function get_currencies()
    {
        return Model_Currency::get_header_currencies();
    }

    /**
     * Get all temporary of time-leave based on request type of user
     *
     * @param integer $account_id account id
     *
     * @return array statistic information
     *
     * @since 1.0
     * @version 1.0
     * @access protected
     * @author Dao Anh Minh
     * @author Nguyen Van Hiep
     */
    protected function get_statistic($account_id = null)
    {
        if (is_null($account_id)) {
            $account_id = $this->user_id;
        }

        // current time leave of user
        $stat     = Model_Counter::find($account_id);
        $requests = Model_Request::get_requests_by_user($account_id);

        $day_off       = 0; // hours
        $paid_vacation = 0; // hours
        $over_time     = 0; // hours

        foreach ($requests as $request) {
            // Ignore requests belonging to request-type which doesn't need to calculate time leave (target_counter: none OR calculation: null OR value_select_type: none)
            if ($request->request_type->target_counter == TYPE_NONE
                    OR $request->request_type->calculation == NOT_CALCULATE
                    OR $request->request_type->value_select_type == NONE_SELECTED
                    OR $request->completion == true
                    OR $request->approval_status == REQUEST_REJECTED) {
                continue;
            }

            $time_requested      = $this->get_time_requested($request);
            $target_counter_type = $request->request_type->target_counter;

            switch ($request->request_type->calculation) {
                case ADDITION :
                    $time_requested = $time_requested; // Positive value
                    break;
                case SUBTRACTION :
                    $time_requested = - $time_requested; // Negative value
                    break;
                default :
                    $time_requested = 0;
                    break;
            }

            switch ($target_counter_type) {
                case TYPE_PAIDVACATION :
                    $paid_vacation = $paid_vacation + $time_requested;
                    break;
                case TYPE_DAYOFF :
                    $day_off       = $day_off + $time_requested;
                    break;
                case TYPE_OVERTIME :
                    $over_time     = $over_time + $time_requested;
                    break;
                case TYPE_NONE :
                    break;
                default :
                    break;
            }
        }

        return array(
            'stat' => $stat,
            'day_off' => $day_off,
            'paid_vacation' => $paid_vacation,
            'over_time' => $over_time
        );
    }

    /**
     * Get time requested of a request
     *
     * @param object $request info of request
     * @return number $time_requested time (hours) requested from a request
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van Hiep
     */
    protected function get_time_requested($request)
    {
        if (($request->opening_time != null) and ( $request->closing_time != null)) {
            $opening_time   = strtotime($request->opening_time);
            $closing_time   = strtotime($request->closing_time);
            $time_requested = round(($closing_time - $opening_time) / 3600, DAY_ROUND);
        } elseif ($request->day_off != null) {
            switch ($request['day_off']) {
                case HALF_DAY_OFF :
                    $time_requested = HALFDAY_OFF; // hour unit
                    break;
                case FULL_DAY_OFF :
                    $time_requested = FULLDAY_OFF; // hour unit
                    break;
                default :
                    $time_requested = NONE; // hour unit
                    break;
            }
        } else {
            $time_requested = 0; // hour unit
        }

        return $time_requested; // hour unit
    }

    /**
     * get all dates in week of month not contains dates of previous month or next month
     *
     * @param integer $month
     * @param integer $year
     * @return array dates in month
     *
     * @access protected
     * @author Dao Anh Minh
     */
    protected function get_dates_in_week_of_month($month, $year)
    {
        $days_in_month  = date("t", mktime(0, 0, 0, $month, 1, $year));
        $weeks_in_month = 1;
        $date_in_week   = array();

        //loop through month
        for ($day = 1; $day <= $days_in_month; $day++) {
            //0..6 sunday...monday
            $position_of_date_in_week                                 = date("w", mktime(0, 0, 0, $month, $day, $year));
            $date_in_week[$weeks_in_month][$position_of_date_in_week] = $year . '/' . $month . '/' . str_pad($day, 2, 0, STR_PAD_LEFT);

            if ($position_of_date_in_week == 6) {
                $weeks_in_month++;
            }
        }

        return $date_in_week;
    }

    /**
     * get full day in week of a month, contains some day of previous month of next month
     * @param integer $month
     * @param integer $year
     * @return array dates in month
     *
     * @access protected
     * @author Dao Anh Minh
     */
    protected function get_all_dates_in_month($month = null, $year = null)
    {
        if (is_null($month) OR is_null($year)) {
            return array();
        }

        $previous_month = date('Y/m/d', date(strtotime('-1 month', strtotime($year . '/' . $month . '/01'))));
        $next_month     = date('Y/m/d', date(strtotime('+1 month', strtotime($year . '/' . $month . '/01'))));

        // get dates in week of month
        $week_of_previous_month = $this->get_dates_in_week_of_month(date('m', strtotime($previous_month)), date('Y', strtotime($previous_month)));
        $week_of_view_month     = $this->get_dates_in_week_of_month($month, $year);
        $week_of_next_month     = $this->get_dates_in_week_of_month(date('m', strtotime($next_month)), date('Y', strtotime($next_month)));

        // the first week of the month contain some dates of previous month
        if (count($week_of_view_month[FIRST_WEEK_IN_MONTH] < 7)) {
            //add some dates of previous month to first week of view month
            $first_week_of_view_month = $week_of_view_month[FIRST_WEEK_IN_MONTH] + $week_of_previous_month[count($week_of_previous_month)];
            //sort date
            ksort($first_week_of_view_month);

            $week_of_view_month[FIRST_WEEK_IN_MONTH] = $first_week_of_view_month;
        }

        // the last week of view month contain some dates of next month
        if (count(end($week_of_view_month)) < 7) {
            //add some dates next month to last week of view month
            $last_week_of_view_month = $week_of_view_month[count($week_of_view_month)] + $week_of_next_month[FIRST_WEEK_IN_MONTH];
            //sort date
            ksort($last_week_of_view_month);

            $week_of_view_month[count($week_of_view_month)] = $last_week_of_view_month;
        }

        return $week_of_view_month;
    }

    /**
     * Get all dates in month including external dates
     *
     * @param integer $month month
     * @param integer $year year
     * @return array dates in month
     *
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected function get_dates_in_month($month, $year)
    {
        $output = array();
        $dates  = $this->get_all_dates_in_month($month, $year);
        foreach ($dates as $week) {
            foreach ($week as $day) {
                $output[] = $day;
            }
        }
        return $output;
    }

    /**
     * Get all Sundays of month
     *
     * @param integer $month month
     * @param integer $year year
     * @return array dates in month
     *
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected function get_sunday_in_month($month, $year)
    {
        $output = array();
        $dates  = $this->get_all_dates_in_month($month, $year);
        foreach ($dates as $week) {
            foreach ($week as $key => $day) {
                if ($key == 0) {
                    $output[] = $day;
                }
            }
        }
        return $output;
    }

    /**
     * Get all Saturdays of month
     *
     * @param integer $month month
     * @param integer $year year
     * @return array dates in month
     *
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected function get_saturday_in_month($month, $year)
    {
        $output = array();
        $dates  = $this->get_all_dates_in_month($month, $year);
        foreach ($dates as $week) {
            foreach ($week as $key => $day) {
                if ($key == 6) {
                    $output[] = $day;
                }
            }
        }
        return $output;
    }

    /**
     * Get dates not belonging to current month
     *
     * @param integer $month month
     * @param integer $year year
     * @return array dates (yyyy/mm/dd) not belonging to current month
     *
     * @access protected
     * @author Nguyen Van Hiep
     */
    protected function get_date_not_belong_to_current_month($month, $year)
    {
        // previous month and next month
        $previous = date('Y/m/d', date(strtotime('-1 month', strtotime($year . '/' . $month . '/01'))));
        $next     = date('Y/m/d', date(strtotime('+1 month', strtotime($year . '/' . $month . '/01'))));

        // get day in week of month
        $pre_month     = $this->get_dates_in_week_of_month(date('m', strtotime($previous)), date('Y', strtotime($previous)));
        $week_in_month = $this->get_dates_in_week_of_month($month, $year);
        $next_month    = $this->get_dates_in_week_of_month(date('m', strtotime($next)), date('Y', strtotime($next)));

        // the first week & last week of month have some day of previous month
        $first_week_dates = (count($week_in_month[1]) < 7) ? $pre_month[count($pre_month)] : array();
        $last_week_dates  = (count(end($week_in_month)) < 7) ? $next_month[1] : array();
        $output_dates     = array_merge($first_week_dates, $last_week_dates);

        return $output_dates;
    }

    /**
     * Get controllers
     *
     * @return list of controlelrs
     *
     * @author Nguyen Van Hiep
     * @access public
     */
    public function get_controller()
    {
        if (count($this->contr) == 0) {
            $this->set_controller(APPPATH . 'classes/controller/');
        }
        $controllers = array();
        $names = array('' => __('message.select_perm'));
        foreach ($this->contr as $contr) {
            if ($contr == 'Controller_Base') {
                $actions = get_class_methods($contr);
            } else {
                $actions = array_diff(get_class_methods($contr), get_class_methods('Controller_Base'));
            }

            $contr = strtolower(str_replace('Controller_', '', $contr));
            $names[$contr] = $contr;
            foreach ($actions as $action) {
                if (preg_match('/^action_/', $action)) {
                    $controllers[$contr][] = str_replace('action_', '', $action);
                }
            }

//            if (($serial === true) and (isset($controllers[$contr]))) {
//                $controllers[$contr] = serialize($controllers[$contr]);
//            }
        }
        return array(
            'controllers' => $controllers,
            'names'       => $names,
            );
    }

    /**
     * Get controllers
     *
     * @param string path
     * @return list of controlelrs
     *
     * @author Nguyen Van Hiep
     * @access protected
     */
    protected function set_controller($path)
    {
        $files  = scandir($path);
        $subdir = str_replace(APPPATH . 'classes/controller/', '', $path);
        $prefix = str_replace(' ', '_', ucfirst(str_replace('/', ' ', $subdir)));
        foreach ($files as $key => $file) {
            if (preg_match('/^\.+/', $file)) {
                unset($files[$key]);
                continue;
            }

            if (preg_match('/\.php$/', $file)) {
                array_push($this->contr, 'Controller_' . $prefix . ucfirst(str_replace('.php', '', $file)));
            } else {
                $path = $path . $file . '/';
                $this->set_controller($path);
            }
        }
    }

    /**
     * Convert specified data to specified character encode
     *
     * @param mixed $data an string or an array need to convert
     * @param string $to_encode expected character encode
     * @return mixed data after converted
     *
     * @access protected
     * @author Dao Anh Minh
     */
    protected function convert_data_to_specified_encode($data = null, $to_encode = 'UTF-8')
    {
        $encoded_data = '';

        //not provided input
        if (is_null($data)) {
            return $encoded_data;
        }

        //data is string -> convert string to specified encode
        if (is_string($data)) {
            $encoded_data = mb_convert_encoding($data, $to_encode);
        }

        //data is array
        if (is_array($data)) {
            //convert all items in array to specified encode
            array_walk_recursive($data, function(&$item, $key, $to_encode_item) {
                $item = mb_convert_encoding($item, $to_encode_item);
            }, $to_encode);

            $encoded_data = $data;
        }

        return $encoded_data;
    }

    /**
     * Prepare data for history
     *
     * @return array prepared data to pass to view
     *
     * @access public
     * @author Nguyen Van Hiep
     */
    public static function prepare_data_to_history()
    {
        // 1. Get current month
        // 2. Send current month to view
        // 3. Get all request-types
        // 4. Set selected-type to be all-types at beginning.
        // 5. Get all positions
        $current_month         = date(DATE_FORMAT, time());
        $data['month_view']    = $current_month;
        $data['types']         = Model_Mstrequesttype::get_all_types();
        $data['type_selected'] = ALL_REQUEST_TYPES;
        $data['all_positions'] = Model_Mstshiftposition::get_all_position();

        // Validation
        $val = Validation::forge('month_view');
        $val->add_field('month_view', __('request.month_view'), 'required|month_date');

        //get posted day and position
        if (Input::method() == 'POST') {
            $post                  = Input::post();
            $data['type_selected'] = $post['request_type'];
            if ($val->run()) {
                $data['month_view'] = $post['month_view'];
            } else {
                $data['month_view'] = '';
            }
            //check click previous or next or current month button
            switch ($post) {
                case key_exists('pre_month', $post) :
                    $data['month_view'] = $post['pre_mth_input'];
                    break;
                case key_exists('next_month', $post) :
                    $data['month_view'] = $post['nxt_mth_input'];
                    break;
                case key_exists('current_month', $post) :
                    $data['month_view'] = $current_month;
                    break;
            }
        }

        //calulate the next and previous month
        $pre_month = date(DATE_FORMAT, strtotime('-1 month', strtotime($data['month_view'] . '/01')));
        $nxt_month = date(DATE_FORMAT, strtotime('+1 month', strtotime($data['month_view'] . '/01')));
        //if month input with empty value, month_view = current month
        if (strlen($data['month_view']) == 0) {
            $data['pre_mth'] = date(DATE_FORMAT, strtotime('-1 month', strtotime($data['month_view'] . '/01')));
            $data['nxt_mth'] = date(DATE_FORMAT, strtotime('+1 month', strtotime($data['month_view'] . '/01')));
        } else {
            $data['pre_mth'] = $pre_month;
            $data['nxt_mth'] = $nxt_month;
        }
        $data['err'] = $val->error_message();
        return $data;
    }

    /*
     * php delete function that deals with directories recursively
     */

    public static function delete_files($target)
    {
        if (!is_link($target) && is_dir($target)) {
            // it's a directory; recursively delete everything in it
            $files = array_diff(scandir($target), array('.', '..'));
            foreach ($files as $file) {
                self::delete_files("$target/$file");
            }
            rmdir($target);
        } else {
            // probably a normal file or a symlink; either way, just unlink() it
            unlink($target);
        }
    }

    /**
     * Get languages
     *
     * @param int $header set languages in header
     * @return array languages
     *
     * @access protected
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    protected function get_languages($header = null)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian",
            "ar" => "Arabic",
            "hy" => "Armenian",
            "eu" => "Basque",
            "bn" => "Bengali",
            "bg" => "Bulgarian",
            "ca" => "Catalan",
            "km" => "Cambodian",
            "zh" => "Chinese (Mandarin)",
            "hr" => "Croatian",
            "cs" => "Czech",
            "da" => "Danish",
            "nl" => "Dutch",
            "en" => "English",
            "et" => "Estonian",
            "fj" => "Fiji",
            "fi" => "Finnish",
            "fr" => "French",
            "ka" => "Georgian",
            "de" => "German",
            "el" => "Greek",
            "gu" => "Gujarati",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "hu" => "Hungarian",
            "is" => "Icelandic",
            "id" => "Indonesian",
            "ga" => "Irish",
            "it" => "Italian",
            "ja" => "Japanese",
            "jw" => "Javanese",
            "ko" => "Korean",
            "la" => "Latin",
            "lv" => "Latvian",
            "lt" => "Lithuanian",
            "mk" => "Macedonian",
            "ms" => "Malay",
            "ml" => "Malayalam",
            "mt" => "Maltese",
            "mi" => "Maori",
            "mr" => "Marathi",
            "mn" => "Mongolian",
            "ne" => "Nepali",
            "no" => "Norwegian",
            "fa" => "Persian",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "pa" => "Punjabi",
            "qu" => "Quechua",
            "ro" => "Romanian",
            "ru" => "Russian",
            "sm" => "Samoan",
            "sr" => "Serbian",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "es" => "Spanish",
            "sw" => "Swahili",
            "sv" => "Swedish?",
            "ta" => "Tamil",
            "tt" => "Tatar",
            "te" => "Telugu",
            "th" => "Thai",
            "bo" => "Tibetan",
            "to" => "Tonga",
            "tr" => "Turkish",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "uz" => "Uzbek",
            "vi" => "Vietnamese",
            "cy" => "Welsh",
            "xh" => "Xhosa",
        );

        if ($header == 'menu') {
            $lang = array(
                '' => 'Language :',
                'en' => 'English'
            );
        } elseif ($header == 'list') {
            $lang = array(
                'en' => 'English'
            );
        } else {
            $lang = array(
                '' => 'Select a language',
                'en' => 'English'
            );
        }

        $dirs = scandir(APPPATH . 'lang/');
        foreach ($dirs as $dir) {
            if ((is_dir(APPPATH . 'lang/' . $dir)) and ( array_key_exists($dir, $languages))) {
                $lang[$dir] = $languages[$dir];
            }
        }
        return $lang;
    }

    /**
     * Get all languages
     *
     * @return array languages
     *
     * @access protected
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    protected function get_all_languages()
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian",
            "ar" => "Arabic",
            "hy" => "Armenian",
            "eu" => "Basque",
            "bn" => "Bengali",
            "bg" => "Bulgarian",
            "ca" => "Catalan",
            "km" => "Cambodian",
            "zh" => "Chinese (Mandarin)",
            "hr" => "Croatian",
            "cs" => "Czech",
            "da" => "Danish",
            "nl" => "Dutch",
            "en" => "English",
            "et" => "Estonian",
            "fj" => "Fiji",
            "fi" => "Finnish",
            "fr" => "French",
            "ka" => "Georgian",
            "de" => "German",
            "el" => "Greek",
            "gu" => "Gujarati",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "hu" => "Hungarian",
            "is" => "Icelandic",
            "id" => "Indonesian",
            "ga" => "Irish",
            "it" => "Italian",
            "ja" => "Japanese",
            "jw" => "Javanese",
            "ko" => "Korean",
            "la" => "Latin",
            "lv" => "Latvian",
            "lt" => "Lithuanian",
            "mk" => "Macedonian",
            "ms" => "Malay",
            "ml" => "Malayalam",
            "mt" => "Maltese",
            "mi" => "Maori",
            "mr" => "Marathi",
            "mn" => "Mongolian",
            "ne" => "Nepali",
            "no" => "Norwegian",
            "fa" => "Persian",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "pa" => "Punjabi",
            "qu" => "Quechua",
            "ro" => "Romanian",
            "ru" => "Russian",
            "sm" => "Samoan",
            "sr" => "Serbian",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "es" => "Spanish",
            "sw" => "Swahili",
            "sv" => "Swedish?",
            "ta" => "Tamil",
            "tt" => "Tatar",
            "te" => "Telugu",
            "th" => "Thai",
            "bo" => "Tibetan",
            "to" => "Tonga",
            "tr" => "Turkish",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "uz" => "Uzbek",
            "vi" => "Vietnamese",
            "cy" => "Welsh",
            "xh" => "Xhosa",
        );

        return $languages;
    }

    /**
     * Get countries
     *
     * @return array countries
     *
     * @access protected
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    protected function get_countries()
    {
        $nations = array(
            "" => "Select a country",
            "AF" => "Afghanistan",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AG" => "Angola",
            "AI" => "Anguilla",
            "AG" => "Antigua &amp; Barbuda",
            "AR" => "Argentina",
            "AA" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BL" => "Bonaire",
            "BA" => "Bosnia &amp; Herzegovina",
            "BW" => "Botswana",
            "BR" => "Brazil",
            "BC" => "British Indian Ocean Ter",
            "BN" => "Brunei",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "IC" => "Canary Islands",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CD" => "Channel Islands",
            "CL" => "Chile",
            "CN" => "China",
            "CI" => "Christmas Island",
            "CS" => "Cocos Island",
            "CO" => "Colombia",
            "CC" => "Comoros",
            "CG" => "Congo",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "CT" => "Cote D'Ivoire",
            "HR" => "Croatia",
            "CU" => "Cuba",
            "CB" => "Curacao",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "TM" => "East Timor",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FA" => "Falkland Islands",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "FS" => "French Southern Ter",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GB" => "Great Britain",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GN" => "Guinea",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HW" => "Hawaii",
            "HN" => "Honduras",
            "HK" => "Hong Kong",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IA" => "Iran",
            "IQ" => "Iraq",
            "IR" => "Ireland",
            "IM" => "Isle of Man",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "NK" => "Korea North",
            "KS" => "Korea South",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "LA" => "Laos",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macau",
            "MK" => "Macedonia",
            "MG" => "Madagascar",
            "MY" => "Malaysia",
            "MW" => "Malawi",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "ME" => "Mayotte",
            "MX" => "Mexico",
            "MI" => "Midway Islands",
            "MD" => "Moldova",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar",
            "NA" => "Nambia",
            "NU" => "Nauru",
            "NP" => "Nepal",
            "AN" => "Netherland Antilles",
            "NL" => "Netherlands (Holland, Europe)",
            "NV" => "Nevis",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NW" => "Niue",
            "NF" => "Norfolk Island",
            "NO" => "Norway",
            "OM" => "Oman",
            "PK" => "Pakistan",
            "PW" => "Palau Island",
            "PS" => "Palestine",
            "PA" => "Panama",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PO" => "Pitcairn Island",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "ME" => "Republic of Montenegro",
            "RS" => "Republic of Serbia",
            "RE" => "Reunion",
            "RO" => "Romania",
            "RU" => "Russia",
            "RW" => "Rwanda",
            "NT" => "St Barthelemy",
            "EU" => "St Eustatius",
            "HE" => "St Helena",
            "KN" => "St Kitts-Nevis",
            "LC" => "St Lucia",
            "MB" => "St Maarten",
            "PM" => "St Pierre &amp; Miquelon",
            "VC" => "St Vincent &amp; Grenadines",
            "SP" => "Saipan",
            "SO" => "Samoa",
            "AS" => "Samoa American",
            "SM" => "San Marino",
            "ST" => "Sao Tome &amp; Principe",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "RS" => "Serbia",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "OI" => "Somalia",
            "ZA" => "South Africa",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syria",
            "TA" => "Tahiti",
            "TW" => "Taiwan",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania",
            "TH" => "Thailand",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad &amp; Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TU" => "Turkmenistan",
            "TC" => "Turks &amp; Caicos Is",
            "TV" => "Tuvalu",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "AE" => "United Arab Emirates",
            "GB" => "United Kingdom",
            "US" => "United States of America",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VS" => "Vatican City State",
            "VE" => "Venezuela",
            "VN" => "Vietnam",
            "VB" => "Virgin Islands (Brit)",
            "VA" => "Virgin Islands (USA)",
            "WK" => "Wake Island",
            "WF" => "Wallis &amp; Futana Is",
            "YE" => "Yemen",
            "ZR" => "Zaire",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe"
        );

        return $nations;
    }

    /**
     * Get all currencies
     *
     * @return array currencies
     *
     * @access protected
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    protected function get_all_currencies()
    {
        $currs = array(
            "AFN" => "Afghanistan Afghani",
            "ALL" => "Albanian Lek",
            "DZD" => "Algerian Dinar",
            "USD" => "US Dollar",
            "EUR" => "Euro",
            "AOA" => "Angolan Kwanza",
            "XCD" => "East Caribbean Dollar",
            "ARS" => "Argentine Peso",
            "AMD" => "Armenian Dram",
            "AWG" => "Aruban Guilder",
            "AUD" => "Australian Dollar",
            "AZN" => "Azerbaijan New Manat",
            "BSD" => "Bahamian Dollar",
            "BHD" => "Bahraini Dinar",
            "BDT" => "Bangladeshi Taka",
            "BBD" => "Barbados Dollar",
            "BYR" => "Belarussian Ruble",
            "BZD" => "Belize Dollar",
            "XOF" => "CFA Franc BCEAO",
            "BMD" => "Bermudian Dollar",
            "BTN" => "Bhutan Ngultrum",
            "BOB" => "Boliviano",
            "BAM" => "Marka",
            "BWP" => "Botswana Pula",
            "NOK" => "Norwegian Krone",
            "BRL" => "Brazilian Real",
            "BND" => "Brunei Dollar",
            "BGN" => "Bulgarian Lev",
            "BIF" => "Burundi Franc",
            "KHR" => "Kampuchean Riel",
            "XAF" => "CFA Franc BEAC",
            "CAD" => "Canadian Dollar",
            "CVE" => "Cape Verde Escudo",
            "KYD" => "Cayman Islands Dollar",
            "CLP" => "Chilean Peso",
            "CNY" => "Yuan Renminbi",
            "COP" => "Colombian Peso",
            "KMF" => "Comoros Franc",
            "CDF" => "Francs",
            "NZD" => "New Zealand Dollar",
            "CRC" => "Costa Rican Colon",
            "HRK" => "Croatian Kuna",
            "CUP" => "Cuban Peso",
            "CZK" => "Czech Koruna",
            "DKK" => "Danish Krone",
            "DJF" => "Djibouti Franc",
            "DOP" => "Dominican Peso",
            "ECS" => "Ecuador Sucre",
            "EGP" => "Egyptian Pound",
            "SVC" => "El Salvador Colon",
            "ERN" => "Eritrean Nakfa",
            "ETB" => "Ethiopian Birr",
            "FKP" => "Falkland Islands Pound",
            "FJD" => "Fiji Dollar",
            "GMD" => "Gambian Dalasi",
            "GEL" => "Georgian Lari",
            "GHS" => "Ghanaian Cedi",
            "GIP" => "Gibraltar Pound",
            "GBP" => "Pound Sterling",
            "QTQ" => "Guatemalan Quetzal",
            "GGP" => "Pound Sterling",
            "GNF" => "Guinea Franc",
            "GWP" => "Guinea-Bissau Peso",
            "GYD" => "Guyana Dollar",
            "HTG" => "Haitian Gourde",
            "HNL" => "Honduran Lempira",
            "HKD" => "Hong Kong Dollar",
            "HUF" => "Hungarian Forint",
            "ISK" => "Iceland Krona",
            "INR" => "Indian Rupee",
            "IDR" => "Indonesian Rupiah",
            "IRR" => "Iranian Rial",
            "IQD" => "Iraqi Dinar",
            "ILS" => "Israeli New Shekel",
            "JMD" => "Jamaican Dollar",
            "JPY" => "Japanese Yen",
            "JOD" => "Jordanian Dinar",
            "KZT" => "Kazakhstan Tenge",
            "KES" => "Kenyan Shilling",
            "KPW" => "North Korean Won",
            "KRW" => "Korean Won",
            "KWD" => "Kuwaiti Dinar",
            "KGS" => "Som",
            "LAK" => "Lao Kip",
            "LVL" => "Latvian Lats",
            "LBP" => "Lebanese Pound",
            "LSL" => "Lesotho Loti",
            "LRD" => "Liberian Dollar",
            "LYD" => "Libyan Dinar",
            "CHF" => "Swiss Franc",
            "LTL" => "Lithuanian Litas",
            "MOP" => "Macau Pataca",
            "MKD" => "Denar",
            "MGF" => "Malagasy Franc",
            "MWK" => "Malawi Kwacha",
            "MYR" => "Malaysian Ringgit",
            "MVR" => "Maldive Rufiyaa",
            "MRO" => "Mauritanian Ouguiya",
            "MUR" => "Mauritius Rupee",
            "MXN" => "Mexican Nuevo Peso",
            "MDL" => "Moldovan Leu",
            "MNT" => "Mongolian Tugrik",
            "MAD" => "Moroccan Dirham",
            "MZN" => "Mozambique Metical",
            "MMK" => "Myanmar Kyat",
            "NAD" => "Namibian Dollar",
            "NPR" => "Nepalese Rupee",
            "ANG" => "Netherlands Antillean Guilder",
            "XPF" => "CFP Franc",
            "NIO" => "Nicaraguan Cordoba Oro",
            "NGN" => "Nigerian Naira",
            "OMR" => "Omani Rial",
            "PKR" => "Pakistan Rupee",
            "PAB" => "Panamanian Balboa",
            "PGK" => "Papua New Guinea Kina",
            "PYG" => "Paraguay Guarani",
            "PEN" => "Peruvian Nuevo Sol",
            "PHP" => "Philippine Peso",
            "PLN" => "Polish Zloty",
            "QAR" => "Qatari Rial",
            "RON" => "Romanian New Leu",
            "RUB" => "Russian Ruble",
            "RWF" => "Rwanda Franc",
            "SHP" => "St. Helena Pound",
            "WST" => "Samoan Tala",
            "STD" => "Dobra",
            "SAR" => "Saudi Riyal",
            "RSD" => "Dinar",
            "SCR" => "Seychelles Rupee",
            "SLL" => "Sierra Leone Leone",
            "SGD" => "Singapore Dollar",
            "SBD" => "Solomon Islands Dollar",
            "SOS" => "Somali Shilling",
            "ZAR" => "South African Rand",
            "SSP" => "South Sudan Pound",
            "LKR" => "Sri Lanka Rupee",
            "SDG" => "Sudanese Pound",
            "SRD" => "Surinam Dollar",
            "SZL" => "Swaziland Lilangeni",
            "SEK" => "Swedish Krona",
            "SYP" => "Syrian Pound",
            "TWD" => "Taiwan Dollar",
            "TJS" => "Tajik Somoni",
            "TZS" => "Tanzanian Shilling",
            "THB" => "Thai Baht",
            "TOP" => "Tongan Pa'anga",
            "TTD" => "Trinidad and Tobago Dollar",
            "TND" => "Tunisian Dollar",
            "TRY" => "Turkish Lira",
            "TMT" => "Manat",
            "UGX" => "Uganda Shilling",
            "UAH" => "Ukraine Hryvnia",
            "AED" => "Arab Emirates Dirham",
            "UYU" => "Uruguayan Peso",
            "UZS" => "Uzbekistan Sum",
            "VUV" => "Vanuatu Vatu",
            "VEF" => "Venezuelan Bolivar",
            "VND" => "Vietnamese Dong",
            "YER" => "Yemeni Rial",
            "ZMW" => "Zambian Kwacha",
            "ZWD" => "Zimbabwe Dollar"
        );

        return $currs;
    }

}

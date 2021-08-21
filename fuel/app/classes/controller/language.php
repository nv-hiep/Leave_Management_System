<?php

/**
 * /language.php
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
class Controller_Language extends Controller_Base
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
        $view        = View::forge('lang/index');
        $view->langs = $this->get_languages('list');

        $this->template->title   = 'Languages';
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
        $view      = View::forge('lang/add');
        $view->err = '';

        $langs      = $this->get_all_languages();
        $langs_used = $this->get_languages('list');
        $others     = array_diff($langs, $langs_used);
        $others     = array('' => 'Select a Language') + $others;

        if (Input::method() == 'POST') {
            $new_lang = Input::post('lang');
            if (strlen($new_lang) == 0) {
                Session::set_flash('error', __('message.validation_error'));
                $view->err = __('message.select_lang');
            } elseif (array_key_exists($new_lang, $langs_used)) {
                Session::set_flash('error', __('message.validation_error'));
                $view->err = __('message.lang_used');
            } else {
                $this->copy_dir(APPPATH . 'lang/en', APPPATH . 'lang\\' . $new_lang);
                Session::set_flash('success', __('message.lang_added'));
                Response::redirect('language');
            }
        }

        $view->langs             = $others;
        $this->template->title   = 'Add new language';
        $this->template->content = $view;
    }

    /**
     * Action edit info
     *
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    public function action_edit($lang = null)
    {
        $langs_used = $this->get_languages('list');

        if ((!array_key_exists($lang, $langs_used)) or ( !file_exists(APPPATH . '/lang/' . $lang))) {
            Session::set_flash('error', __('message.lang_not_exists'));
            Response::redirect('language/');
        }
        $cfile_path = APPPATH . '/lang/' . $lang . '/language.ini';
        $vfile_path = APPPATH . '/lang/' . $lang . '/validation.php';
        $view       = View::forge('lang/edit');
        $view->errc = '';
        $view->errv = '';

        $view->content     = file_get_contents($cfile_path);
        $view->val_content = file_get_contents($vfile_path);

        if (Input::method() == 'POST') {
            $content     = Input::post('content');
            $val_content = Input::post('val_content');
            if ((strlen($content) == 0) or ( strlen($val_content) == 0)) {
                Session::set_flash('error', __('message.validation_error'));
                $view->errc = (strlen($content) == 0) ? __('message.add_content') : '';
                $view->errv = (strlen($val_content) == 0) ? __('message.add_content') : '';
            } else {
                $chandle = fopen($cfile_path, "w");
                $vhandle = fopen($cfile_path, "w");
                if ((fwrite($chandle, $content) == false) or ( fwrite($vhandle, $val_content) == false)) {
                    Session::set_flash('error', __('message.cannot_edit_file'));
                } else {
                    Session::set_flash('success', __('message.file_edited'));
                }
                fclose($chandle);
                fclose($vhandle);
            }
        }

        $view->abbr              = $lang;
        $view->lang_editing      = $langs_used[$lang];
        $this->template->title   = 'Edit language file';
        $this->template->content = $view;
    }

    /**
     * Action change password
     *
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    public function action_delete($lang = null)
    {
        if ($lang == 'en') {
            Session::set_flash('error', __('message.cannot_delete_lang'));
            Response::redirect('language/');
        }
        $languages = $this->get_all_languages();

        if ((!array_key_exists($lang, $languages)) or ( !file_exists(APPPATH . '/lang/' . $lang))) {
            Session::set_flash('error', __('message.lang_not_exists'));
            Response::redirect('language/');
        }

        Controller_Base::delete_files(APPPATH . 'lang/' . $lang);
        if (!file_exists(APPPATH . '/lang/' . $lang)) {
            Session::set_flash('success', __('message.lang_deleted'));
            Response::redirect('language/');
        } else {
            Session::set_flash('error', __('message.cannot_delete_lang'));
            Response::redirect('language/');
        }
    }

    /**
     * Copy entire folder
     *
     * @return void
     *
     * @access public
     * @since 1.0
     * @version 1.0
     * @author Nguyen Van hiep
     */
    public function copy_dir($source, $dest)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, 0755);
        }

        // Loop through the folder
        $dir   = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->copy_dir("$source/$entry", "$dest/$entry");
        }

        // Clean up
        $dir->close();
        return true;
    }

}

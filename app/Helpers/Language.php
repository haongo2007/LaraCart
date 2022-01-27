<?php

use App\Models\Front\ShopLanguage;

if (!function_exists('lc_language_all')) {
    //Get all language
    function lc_language_all()
    {
        return ShopLanguage::getListActive();
    }
}

if (!function_exists('lc_language_render')) {
    /*
    Render language
     */
    function lc_language_render($string)
    {
        $arrCheck = explode('lang::', $string);
        if (count($arrCheck) == 2) {
            return trans($arrCheck[1]);
        } else {
            return trans($string);
        }
    }
}


if (!function_exists('lc_get_locale')) {
    /*
    Get locale
    */
    function lc_get_locale()
    {
        return app()->getLocale();
    }
}


if (!function_exists('lc_lang_switch')) {
    /**
     * Switch language
     *
     * @param   [string]  $lang
     *
     * @return  [mix]
     */
    function lc_lang_switch($lang = null)
    {
        if (!$lang) {
            return ;
        }

        $languages = lc_language_all()->keys()->all();
        if (in_array($lang, $languages)) {
            app()->setLocale($lang);
            session(['locale' => $lang]);
        } else {
            return abort(404);
        }

    }
}
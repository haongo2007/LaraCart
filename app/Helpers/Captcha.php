<?php

if (!function_exists('lc_captcha_method')) {
    function lc_captcha_method()
    {
        //If function captcha disable or dont setup
        if(empty(lc_config('captcha_mode'))) {
            return null;
        }

        // If method captcha selected
        if(!empty(lc_config('captcha_method'))) {
            $moduleClass = lc_config('captcha_method');
            //If class plugin captcha exist
            if(class_exists($moduleClass)) {
                //Check plugin captcha disable
                $key = (new $moduleClass)->configKey;
                if(lc_config($key)) {
                    return (new $moduleClass);
                } else {
                    return null;
                }
            }
        }
        return null;

    }
}

if (!function_exists('lc_captcha_page')) {
    function lc_captcha_page()
    {
        if(empty(lc_config('captcha_page'))) {
            return [];
        }

        if(!empty(lc_config('captcha_page'))) {
            return json_decode(lc_config('captcha_page'));
        }
    }
}

if (!function_exists('lc_get_plugin_captcha_installed')) {
    /**
     * Get all class plugin captcha installed
     *
     * @param   [string]  $code  Payment, Shipping
     *
     */
    function lc_get_plugin_captcha_installed($onlyActive = true)
    {
        $listPluginInstalled =  \BlackCart\Core\Admin\Models\AdminConfig::getPluginCaptchaCode($onlyActive);
        $arrPlugin = [];
        if($listPluginInstalled) {
            foreach ($listPluginInstalled as $key => $plugin) {
                $keyPlugin = lc_word_format_class($plugin->key);
                $pathPlugin = app_path() . '/Plugins/Other/'.$keyPlugin;
                $nameSpaceConfig = '\App\Plugins\Other\\'.$keyPlugin.'\AppConfig';
                if (file_exists($pathPlugin . '/AppConfig.php') && class_exists($nameSpaceConfig)) {
                    $arrPlugin[$nameSpaceConfig] = lc_language_render($plugin->detail);
                }
            }
        }
        return $arrPlugin;
    }
}
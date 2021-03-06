<?php  

if (!function_exists('lc_get_all_plugin')) {
    /**
     * Get all class plugin
     *
     * @param   [string]  $code  Payment, Shipping
     *
     * @return  [array] 
     */
    function lc_get_all_plugin($code)
    {
        $code = lc_word_format_class($code);
        $arrClass = [];
        $dirs = array_filter(glob(app_path() . '/Plugins/' . $code . '/*'), 'is_dir');
        if ($dirs) {
            foreach ($dirs as $dir) {
                $tmp = explode('/', $dir);
                $nameSpace = '\App\Plugins\\' . $code . '\\' . end($tmp);
                $nameSpaceConfig = $nameSpace . '\\AppConfig';
                if (file_exists($dir . '/AppConfig.php') && class_exists($nameSpaceConfig)) {
                    $arrClass[end($tmp)] = $nameSpace;
                }
            }
        }
        return $arrClass;
    }
}

if (!function_exists('lc_get_plugin_installed')) {
    /**
     * Get all class plugin
     *
     * @param   [string]  $code  Payment, Shipping
     *
     */
    function lc_get_plugin_installed($code = null, $onlyActive = true)
    {
        return \App\Models\Admin\Config::getPluginCode($code, $onlyActive);
    }
}




if (!function_exists('lc_get_all_plugin_actived')) {
    /**
     * Get all class plugin actived
     *
     * @param   [string]  $code  Payment, Shipping
     *
     * @return  [array] 
     */
    function lc_get_all_plugin_actived($code)
    {
        $code = lc_word_format_class($code);
        
        $pluginsActived = [];
        $allPlugins = lc_get_all_plugin($code);
        if(count($allPlugins)){
            foreach ($allPlugins as $keyPlugin => $plugin) {
                if(lc_config($keyPlugin) && lc_config($keyPlugin)['value'] == 1){
                    $pluginsActived[$keyPlugin] = $plugin;
                }
            }
        }
        return $pluginsActived;
    }
}


    /**
     * Get namespace plugin controller
     *
     * @param   [string]  $code  Shipping, Payment,..
     * @param   [string]  $key  Paypal,..
     *
     * @return  [array] 
     */

    if (!function_exists('lc_get_class_plugin_controller')) {
        function lc_get_class_plugin_controller($code, $key = null){
            if ($key == null) {
                return null;
            }
            
            $code = lc_word_format_class($code);
            $key = lc_word_format_class($key);

            $nameSpace = lc_get_plugin_namespace($code, $key);
            $nameSpace = $nameSpace . '\Controllers\FrontController';

            return $nameSpace;
        }
    }


    /**
     * Get namespace plugin config
     *
     * @param   [string]  $code  Shipping, Payment,..
     * @param   [string]  $key  Paypal,..
     *
     * @return  [array] 
     */
    if (!function_exists('lc_get_class_plugin_config')) {
        function lc_get_class_plugin_config($code, $key){

            $code = lc_word_format_class($code);
            $key = lc_word_format_class($key);

            $nameSpace = lc_get_plugin_namespace($code, $key);
            $nameSpace = $nameSpace . '\AppConfig';

            return $nameSpace;
        }
    }

    /**
     * Get namespace module
     *
     * @param   [string]  $code  Block, Cms, Payment, shipping..
     * @param   [string]  $key  Content,Paypal, Cash..
     *
     * @return  [array] 
     */
    if (!function_exists('lc_get_plugin_namespace')) {
        function lc_get_plugin_namespace($code, $key)
        {
            $code = lc_word_format_class($code);
            $key = lc_word_format_class($key);
            $nameSpace = '\App\Plugins\\'.$code.'\\' . $key;
            return $nameSpace;
        }
    }
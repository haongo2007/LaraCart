<?php 
use App\Models\Admin\Config;
use App\Models\Admin\Store;

if (!function_exists('lc_admin_can_config')) {
    /**
     * Get value config from table lc_admin_can_config
     *
     * @return  [type]          [return description]
     */
    function lc_admin_can_config()
    {
        return \App\Models\Admin\Admin::user()->checkPermissionconfig();
    }
}

if (!function_exists('lc_config')) {
    /**
     * Get value config from table lc_config
     *
     * @param   [string|array] $key      [$key description]
     * @param   [null|int]  $store    Store id.
     *
     * @return  [type]          [return description]
     */
    function lc_config($key = null, $storeId = null)
    {
        $storeId = ($storeId === null) ? config('app.storeId') : $storeId;
        //Update config
        if (is_array($key)) {
            if (count($key) == 1) {
                foreach ($key as $k => $v) {
                    return Config::whereIn('store_id', $storeId)->where('key', $k)->update(['value' => $v]);
                }
            } else {
                return false;
            }
        }
        //End update
        $allConfig = Config::getAllConfigOfStore($storeId);
        if ($key === null) {
            return $allConfig;
        }
        return $allConfig[$key] ?? (lc_config_global()[$key] ?? null);
    }
}


if (!function_exists('lc_config_admin')) {
/**
 * Get config value in adin with session store id
 *
 * @param   [type]$key  [$key description]
 * @param   null        [ description]
 *
 * @return  [type]      [return description]
 */
    function lc_config_admin($key = null)
    {
        return lc_config($key,session('adminStoreId'));
    }
}


if (!function_exists('lc_config_global')) {
    /**
     * Get value config from table lc_config for store_id 0
     *
     * @param   [string|array] $key      [$key description]
     * @param   [null|int]  $store    Store id.
     *
     * @return  [type]          [return description]
     */
    function lc_config_global($key = null,$storeId = 0)
    {
        //Update config
        if (is_array($key)) {
            if (count($key) == 1) {
                foreach ($key as $k => $v) {
                    return Config::where('store_id', $storeId)->where('key', $k)->update(['value' => $v]);
                }
            } else {
                return false;
            }
        }
        //End update
        
        $allConfig = [];
        try {
            $allConfig = Config::getAllGlobal($storeId);
        } catch(\Throwable $e) {
            //
        }
        if ($key === null) {
            return $allConfig;
        }
        if (is_null($allConfig) || !array_key_exists($key, $allConfig)) {
            return null;
        } else {
            return trim($allConfig[$key]);
        }
    }
}

if (!function_exists('lc_config_group')) {
    /*
    Group Config info
     */
    function lc_config_group($group = null, $suffix = null)
    {
        $groupData = Config::getGroup($group, $suffix);
        return $groupData;
    }
}


if (!function_exists('lc_store')) {
    /**
     * Get info store
     *
     * @param   [string] $key      [$key description]
     * @param   [null|int]  $store    store id
     *
     * @return  [mix] 
     */
    function lc_store($key = null, $store = null)
    {
        $store = ($store == null) ? config('app.storeId') : $store;
        //Update store info
        if (is_array($key)) {
            if (count($key) == 1) {
                foreach ($key as $k => $v) {
                    return Store::where('id', $store)->update([$k => $v]);
                }
            } else {
                return false;
            }
        }
        //End update

        $allStoreInfo = [];
        try {
            $allStoreInfo = Store::getListAll()[$store]->toArray() ?? [];
        } catch(\Throwable $e) {
            //
        }

        $lang = app()->getLocale();
        $descriptions = $allStoreInfo['descriptions'] ?? [];
        foreach ($descriptions as $row) {
            if ($lang == $row['lang']) {
                $allStoreInfo += $row;
            }
        }
        if ($key == null) {
            return $allStoreInfo;
        }
        return $allStoreInfo[$key] ?? null;
    }
}

if (!function_exists('lc_store_active')) {
    function lc_store_active($field = null) {
        switch ($field) {
            case 'code':
                return Store::getCodeActive();
                break;

            case 'domain':
                return Store::getStoreActive();
                break;

            default:
                return Store::getListAllActive();
                break;
        }
    }
}


if (!function_exists('lc_uuid')) {
    /**
     * Generate UUID
     *
     * @param   [string]  $name
     * @param   [array]  $param
     *
     * @return  [type]         [return description]
     */
    function lc_uuid()
    {
        return (string)\Illuminate\Support\Str::orderedUuid();
    }
}

if (!function_exists('lc_generate_id')) {
    /**
     * Generate ID
     *
     * @param   [type]  $type  [$type description]
     *
     * @return  [type]         [return description]
     */
    function lc_generate_id($type = null)
    {
        switch ($type) {
            case 'shop_store':
                return 'S-'.lc_token(5).'-'.lc_token(5);
                break;
            case 'shop_order':
                return 'O-'.lc_token(5).'-'.lc_token(5);
                break;
            
            default:
                return lc_uuid();
                break;
        }
    }
}
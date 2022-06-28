<?php

use App\Models\Front\ShopLanguage;
use \Illuminate\Support\Facades\Cache;

if (!function_exists('lc_clear_cache')) {
    /**
     * Clear cache
     *
     * @param   [string]  $domain
     *
     * @return  [string]         [$domain]
     */
    function lc_clear_cache($typeCache = 'cache_all', $storeId = null)
    {
        try {
            $storeId = $storeId ?? session('adminStoreId');
            if($typeCache == 'cache_all') {
                Cache::flush();
            } else {
                $arrCacheLocal = [];
                $arrLang = ShopLanguage::getCodeAll();
                foreach ($arrLang as $code => $name) {
                    $arrCacheLocal['cache_category'][]      = 'cache_category_'.$code;
                    $arrCacheLocal['cache_product'][]       = 'cache_product_'.$code;
                    $arrCacheLocal['cache_product'][]       = 'cache_product_special_'.$code;
                    $arrCacheLocal['cache_news'][]          = 'cache_news_'.$code;
                    $arrCacheLocal['cache_category_cms'][]  = 'cache_category_cms_'.$code;
                    $arrCacheLocal['cache_content_cms'][]   = 'cache_content_cms_'.$code;
                    $arrCacheLocal['cache_page'][]          = 'cache_page_'.$code;
                    $arrCacheLocal['cache_country'][]       = 'cache_page_'.$code;
                }
                Cache::forget($typeCache);
                if (!empty($arrCacheLocal[$typeCache])) {
                    foreach ($arrCacheLocal[$typeCache] as  $cacheIndex) {
                        Cache::forget($cacheIndex);
                        Cache::forget($storeId.'_'.$cacheIndex);
                    }
                }
            }
            $response = ['error' => 0, 'msg' => 'Clear success!', 'action' => $typeCache];
        }  catch (\Throwable $e) {
            $response = ['error' => 1, 'msg' => $e->getMessage(), 'action' => $typeCache];
        }
        return $response;

    }
}

if (!function_exists('lc_set_cache')) {
/**
 * [lc_set_cache description]
 *
 * @param   [string]$cacheIndex  [$cacheIndex description]
 * @param   [type]$value       [$value description]
 * @param   [seconds]$time        [$time description]
 * @param   null               [ description]
 *
 * @return  [type]             [return description]
 */
    function lc_set_cache($cacheIndex , $value,$storeId, $time = null)
    {
        if(empty($cacheIndex)) {
            return ;
        }
        $seconds = $time ?? (lc_config_global('cache_time',$storeId) ?? 600);
        
        Cache::put($cacheIndex, $value, $seconds);
    }
}
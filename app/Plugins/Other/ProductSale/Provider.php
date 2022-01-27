<?php
    $this->loadTranslationsFrom(__DIR__.'/Lang', 'Plugins/Other/ProductSale');
    $this->loadViewsFrom(__DIR__.'/Views', 'Plugins/Other/ProductSale');
    // $this->mergeConfigFrom(
    //     __DIR__.'/config.php', 'key_define_for_plugin'
    // );
    if(lc_config('ProductSale')) {
        /**
         *Check product flash sale over stock
        */
        if (!function_exists('lc_product_flash_check_over')) {
            function lc_product_flash_check_over($pId, $qty) {
                $product = (new \App\Plugins\Other\ProductSale\Models\PluginModel)
                    ->where('product_id', $pId)
                    ->first();
                if ($product && ((int)$product ->stock - (int)$product->sold) < $qty) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        /**
         * Update stock flash sale
         */
        if (!function_exists('lc_product_flash_update_stock')) {
            function lc_product_flash_update_stock($pId, $qty) {
                $product = (new \App\Plugins\Other\ProductSale\Models\PluginModel)
                    ->where('product_id', $pId)
                    ->first();
                if ($product) {
                    $product->sold = $product->sold + $qty;
                    $product->save();
                }
            }
        }

        /**
         * Get top flash sale
         */
        if (!function_exists('lc_product_flash')) {
            function lc_product_flash($limit = 8, $paginate = false) {
                $productFlash = (new \App\Plugins\Other\ProductSale\Models\PluginModel)->getProductFlash($limit, $paginate);
                return $productFlash;
            }
            
        }
        /**
         * Get path public flash sale
         */
        if (!function_exists('lc_public_path_flash')) {
            function lc_public_path_flash($file) {
                $pathFlash = new \App\Plugins\Other\ProductSale\AppConfig;
                return $pathFlash->pathPlugin.$file;
            }
            
        }
    }
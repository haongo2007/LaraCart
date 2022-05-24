<?php
use App\Models\Front\ShopCurrency;

if (!function_exists('lc_currency_render')) {
    /**
     * Render currency: format number, change amount, add symbol
     *
     * @param   float  $money                 [$money description]
     * @param   [type] $currency              [$currency description]
     * @param   null   $rate                  [$rate description]
     * @param   null   $space_between_symbol  [$space_between_symbol description]
     * @param   false  $useSymbol             [$useSymbol description]
     * @param   true                          [ description]
     *
     * @return  [type]                        [return description]
     */
    function lc_currency_render(float $money, $currency = null, $rate = null, $space_between_symbol = false, $useSymbol = true)
    {
        return ShopCurrency::render($money, $currency, $rate, $space_between_symbol, $useSymbol);
    }
}

if (!function_exists('lc_currency_render_symbol')) {
    /**
     * Only render symbol, dont change amount
     *
     * @param   float  $money                 [$money description]
     * @param   [type] $currency              [$currency description]
     * @param   null   $space_between_symbol  [$space_between_symbol description]
     * @param   false  $include_symbol        [$include_symbol description]
     * @param   true                          [ description]
     *
     * @return  [type]                        [return description]
     */
    function lc_currency_render_symbol(float $money, $currency = null, $space_between_symbol = false, $include_symbol = true)
    {
        $currency = $currency ? $currency : lc_currency_code();
        return ShopCurrency::onlyRender($money, $currency, $space_between_symbol, $include_symbol);
    }
}


if (!function_exists('lc_currency_value')) {
/**
 * Get value of amount with specify exchange rate
 * if dont specify rate, will use exchange rate default
 *
 * @param   float  $money  [$money description]
 * @param   float  $rate   [$rate description]
 * @param   null           [ description]
 *
 * @return  [type]         [return description]
 */
    function lc_currency_value(float $money, float $rate = null)
    {
        return ShopCurrency::getValue($money, $rate);
    }
}

//Get code currency
if (!function_exists('lc_currency_code')) {
    function lc_currency_code()
    {
        return ShopCurrency::getCode();
    }
}

//Get symbol currency
if (!function_exists('lc_currency_symbol')) {
    function lc_currency_symbol()
    {
        return ShopCurrency::getSymbol();
    }
}

//Get rate currency
if (!function_exists('lc_currency_rate')) {
    function lc_currency_rate()
    {
        return ShopCurrency::getRate();
    }
}

//Format value without symbol
if (!function_exists('lc_currency_format')) {
    function lc_currency_format(float $money)
    {
        return ShopCurrency::format($money);
    }
}

//Get currency info
if (!function_exists('lc_currency_info')) {
    function lc_currency_info()
    {
        return ShopCurrency::getCurrency();
    }
}

//Get all currencies
if (!function_exists('lc_currency_all')) {
    function lc_currency_all()
    {
        return ShopCurrency::getListActive();
    }
}

//Get all currencies active
if (!function_exists('lc_currency_all_active')) {
    function lc_currency_all_active()
    {
        return ShopCurrency::getCodeActive();
    }
}

//Convert to origin price to search
if (!function_exists('lc_convert_price_to_origin')) {
    function lc_convert_price_to_origin(float $money)
    {
        $code = ShopCurrency::getCode();     
        if ($code != 'USD') {
            return round($money / intval(ShopCurrency::getRate()));
        }else{
            return $money;
        }
    }
}

//Get currencies default
if (!function_exists('lc_currency_default')) {
    function lc_currency_default()
    {
        $currencyList = ShopCurrency::where('store_id', 0)->paginate();
        return $currencyList;
    }
}

<?php

namespace App\Http\Middleware\Front;

use App\Models\Front\ShopCurrency;
use Closure;
use Session;

class Currency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currency = session('currency') ?? lc_store('currency');
        if(!array_key_exists($currency, lc_currency_all_active())){
            $currency = array_key_first(lc_currency_all_active());
        }
        ShopCurrency::setCode($currency);
        return $next($request);
    }
}

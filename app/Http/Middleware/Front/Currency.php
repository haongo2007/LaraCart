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
        $currentCurrency = $request->header('x-currency');
        if(!array_key_exists($currentCurrency, lc_currency_all_active())){
            $currentCurrency = array_key_first(lc_currency_all_active());
        }
        ShopCurrency::setCode($currentCurrency);
        return $next($request);
    }
}

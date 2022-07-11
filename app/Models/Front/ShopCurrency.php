<?php
/**
 * @author Lanh Le <lanhktc@gmail.com>
 */
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopCurrency extends Model
{
    public $table = 'shop_currency';
    public $timestamps                  = false;
    protected static $code              = '';
    protected static $name              = '';
    protected static $symbol            = '';
    protected static $exchange_rate     = 1;
    protected static $precision         = 0;
    protected static $symbol_first      = 0;
    protected static $thousands         = ',';
    protected static $decimal           = '.';
    protected static $list              = null;
    protected static $getArray          = null;
    protected static $getCodeActive     = null;
    protected static $checkListCurrency = [];
    protected $guarded                  = [];
    private static $getListActive      = null;
    const ITEM_PER_PAGE = 15;
    const STATUS = [1];


    public function getCurrencyListAdmin($searchParams='')
    {
        $sort_order = $searchParams['sort_order'] ?? 'id_desc';
        $keyword = $searchParams['keyword'] ?? '';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        $status = $searchParams['status'] ?? self::STATUS;
        $arrSort = [
            'id__desc' => trans('currency.admin.sort_order.id_desc'),
            'id__asc' => trans('currency.admin.sort_order.id_asc'),
            'name__desc' => trans('currency.admin.sort_order.name_desc'),
            'name__asc' => trans('currency.admin.sort_order.name_asc'),
        ];

        $currencyList = new ShopCurrency;
        if ($keyword) {
            $currencyList = $currencyList->where(function ($sql) use($keyword){
                $sql->where('code', 'like', '%' . $keyword . '%')->orWhere('name', 'like', '%' . $keyword . '%');
            });
        }

        if ($status) {
            $currencyList = $currencyList->whereIn('status',$status);
        }

        $currencyList = $currencyList->whereIn('store_id', session('adminStoreId'));

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $currencyList = $currencyList->orderBy($field, $sort_field);

        } else {
            $currencyList = $currencyList->orderBy('id', 'desc');
        }
        return $currencyList->paginate($limit);
    }

    public static function getListAll()
    {
        if (!self::$list) {
            self::$list = self::get()
                ->keyBy('code');
        }
        return self::$list;
    }

    public static function getCodeActive()
    {
        if (self::$getCodeActive === null) {
            self::$getCodeActive = self::where('status', 1)
                ->pluck('name', 'code')
                ->all();
        }
        return self::$getCodeActive;
    }


    public static function getCodeAll()
    {
        if (self::$getArray === null) {
            self::$getArray = self::pluck('name', 'code')->all();
        }
        return self::$getArray;
    }

    /**
     * [setCode description]
     * @param [type] $code [description]
     */
    public static function setCode($code)
    {
        self::$code = $code;
        if (empty(self::$checkListCurrency[$code])) {
            self::$checkListCurrency[$code] = self::where('code', $code)->first();
        }
        $checkCurrency = self::$checkListCurrency[$code];
        if ($checkCurrency) {
            self::$name          = $checkCurrency->name;
            self::$symbol        = $checkCurrency->symbol;
            self::$exchange_rate = $checkCurrency->exchange_rate;
            self::$precision     = $checkCurrency->precision;
            self::$symbol_first  = $checkCurrency->symbol_first;
            self::$thousands     = $checkCurrency->thousands;
            self::$decimal       = ($checkCurrency->thousands == '.') ? ',' : '.';
        }
    }

    /**
     * A order has and belongs to many stores.
     *
     * @return BelongsToMany
     */
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    /**
     * [getCurrency description]
     * @return [type] [description]
     */
    public static function getCurrency()
    {
        return [
            'code'          => self::$code,
            'name'          => self::$name,
            'symbol'        => self::$symbol,
            'exchange_rate' => self::$exchange_rate,
            'precision'     => self::$precision,
            'symbol_first'  => self::$symbol_first,
            'thousands'     => self::$thousands,
            'decimal'       => self::$decimal,
        ];
    }

    /*
     * [getCode description]
     * @return [type] [description]
     */
    public static function getCode()
    {
        return self::$code;
    }
    /*
     * [getCode description]
     * @return [type] [description]
     */
    public static function getSymbol()
    {
        return self::$symbol;
    }

    /**
     * [getRate description]
     * @return [type] [description]
     */
    public static function getRate()
    {
        return self::$exchange_rate;
    }

    /**
     * [getValue description]
     * @param  float  $money [description]
     * @param  [type] $rate  [description]
     * @return [type]        [description]
     */
    public static function getValue(float $money, $rate = null)
    {
        if (!empty($rate)) {
            return $money * $rate;
        } else {
            return $money * self::$exchange_rate;
        }

    }

    /**
     * [format description]
     * @param  float  $money [description]
     * @return [type]        [description]
     */
    public static function format(float $money)
    {
        return number_format($money, self::$precision, self::$decimal, self::$thousands);
    }

    /**
     * [render description]
     * @param  float   $money                [description]
     * @param  [type]  $currency             [description]
     * @param  [type]  $rate                 [description]
     * @param  boolean $space_between_symbol [description]
     * @param  boolean $include_symbol       [description]
     * @return [type]                        [description]
     */
    public static function render(float $money, $currency = null, $rate = null, $space_between_symbol = false, $include_symbol = true)
    {
        $space_symbol = ($space_between_symbol) ? ' ' : '';
        $dataCurrency = self::getCurrency();
        if ($currency) {
            if (empty(self::$checkListCurrency[$currency])) {
                self::$checkListCurrency[$currency] = self::where('code', $currency)->first();
            }
            $checkCurrency = self::$checkListCurrency[$currency];
            if ($checkCurrency) {
                $dataCurrency = $checkCurrency;
            }
        }
        //Get currently value
        $value = self::getValue($money, $rate);

        $symbol = ($include_symbol) ? $dataCurrency['symbol'] : '';

        if ($dataCurrency['symbol_first']) {
            if ($money < 0) {
                return '-' . $symbol . $space_symbol . self::format(abs($value));
            } else {
                return $symbol . $space_symbol . self::format($value);
            }
        } else {
            return self::format($value) . $space_symbol . $symbol;
        }
    }

    /**
     * [onlyRender description]
     * @param  float   $money                [description]
     * @param  [type]  $currency             [description]
     * @param  boolean $space_between_symbol [description]
     * @param  boolean $include_symbol       [description]
     * @return [type]                        [description]
     */
    public static function onlyRender(float $money, $currency, $space_between_symbol = false, $include_symbol = true)
    {
        if (empty(self::$checkListCurrency[$currency])) {
            self::$checkListCurrency[$currency] = self::where('code', $currency)->first();
        }
        $checkCurrency = self::$checkListCurrency[$currency];
        $space_symbol  = ($space_between_symbol) ? ' ' : '';
        $symbol        = ($include_symbol) ? $checkCurrency['symbol'] : '';
        if ($checkCurrency['symbol_first']) {
            if ($money < 0) {
                return '-' . $symbol . $space_symbol . self::format(abs($money));
            } else {
                return $symbol . $space_symbol . self::format($money);
            }

        } else {
            return self::format($money) . $space_symbol . $symbol;
        }
    }
    
    /**
     * Sum value of cart
     *
     * @param   float  $rate     [$rate description]
     *
     * @return  [array]
     */
    public static function sumCart(float $rate = null)
    {
        // $carts = Cart::instance('default')->getItemsGroupByStore();
        // $dataReturn = [];

        // $sumSubtotal  = 0;
        // $sumSubtotalWithTax  = 0;
        // $rate = ($rate) ? $rate : self::$exchange_rate;
        // foreach ($carts as $storeId => $cart) {
        //     $sumSubtotalStore  = 0;
        //     $sumSubtotalWithTaxStore  = 0;
        //     foreach ($cart as $detail) {
        //         $sumValue = $detail->qty * self::getValue($detail->price, $rate);
        //         $sumValueWithTax = $detail->qty * self::getValue(bc_tax_price($detail->price, $detail->tax), $rate);
        //         $sumSubtotal += $sumValue;
        //         $sumSubtotalStore += $sumValue;
        //         $sumSubtotalWithTax +=  $sumValueWithTax;
        //         $sumSubtotalWithTaxStore+= $sumValueWithTax;
        //     }
        //     $dataReturn['store'][$storeId]['subTotal'] = $sumSubtotalStore;
        //     $dataReturn['store'][$storeId]['subTotalWithTax'] = $sumSubtotalWithTaxStore;

        // }
        // $dataReturn['subTotal'] = $sumSubtotal;
        // $dataReturn['subTotalWithTax'] = $sumSubtotalWithTax;
        // return $dataReturn;
    }

    public static function getListRate()
    {
        return self::pluck('exchange_rate', 'code')->all();
    }

    public static function getListActive($storeId)
    {
        if (self::$getListActive === null) {
            $curActive = self::where('status', 1);
            if (!$storeId) {
                $curActive = $curActive->where('store_id',0);
            }else{
                $curActive = $curActive->where('store_id',$storeId);
            }
            $check = $curActive->exists();
            if (!$check) {
                $curActive = $curActive->orWhere('store_id',0);
            }
            $curActive = $curActive->get()->keyBy('code');
            self::$getListActive = $curActive;
        }
        return self::$getListActive;
    }
    //Scort
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }

    protected static function boot() {
        parent::boot();
        static::deleting(function ($model) {
            if(in_array($model->id, LC_GUARD_CURRENCY)){
                return false;
            }
        });
    }

}

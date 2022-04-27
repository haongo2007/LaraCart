<?php
#black-cart/Core/Front/Models/ShopLanguage.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ShopLanguage extends Model
{
    public $table = 'shop_language';
    public $timestamps                = false;
    protected $guarded                = [];
    private static $getListAll      = null;
    private static $getListActive      = null;
    private static $getArrayLanguages = null;
    private static $getCodeActive = null;
    const ITEM_PER_PAGE = 15;
    const ACTIVE = ['1'];

    public function getLanguageListAdmin(array $dataSearch)
    {
        $limit = Arr::get($dataSearch, 'limit', self::ITEM_PER_PAGE);
        $status= Arr::get($dataSearch, 'status', self::ACTIVE);
        $title = Arr::get($dataSearch, 'name', '');
        $code = Arr::get($dataSearch, 'code', []);
        $languageList     = (new ShopLanguage);

        if ($title) {
            $languageList = $languageList->where('name', 'like', '%' . $title . '%');
        }
        if(is_array($code) && count($code) > 1) {
            $languageList = $languageList->whereIn('code', $code);
        }

        if (!is_null($status) && is_array($status)) {
            $languageList = $languageList->whereIn('status',$status);
        }

        $languageList = $languageList->paginate($limit);
        return $languageList;
    }

    public static function getListAll()
    {
        if (self::$getListAll === null) {
            self::$getListAll = self::get()
                ->keyBy('code');
        }
        return self::$getListAll;
    }

    public static function getListActive()
    {
        if (self::$getListActive === null) {
            self::$getListActive = self::where('status', 1)
                ->get()
                ->keyBy('code');
        }
        return self::$getListActive;
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
        if (self::$getArrayLanguages === null) {
            self::$getArrayLanguages = self::pluck('name', 'code')->all();
        }
        return self::$getArrayLanguages;
    }
    
    protected static function boot() {
        parent::boot();
        static::deleting(function ($model) {
            if (in_array($model->id, BC_GUARD_LANGUAGE)) {
                return false;
            }
        });
    }
}

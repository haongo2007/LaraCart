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

    public function getLanguageListAdmin(array $dataSearch)
    {
        $limit = Arr::get($dataSearch, 'limit', self::ITEM_PER_PAGE);
        $title = Arr::get($dataSearch, 'name', '');
        $code = Arr::get($dataSearch, 'code', []);
        $languageList     = (new ShopLanguage);

        if ($title) {
            $languageList = $languageList->where('name', 'like', '%' . $title . '%');
        }
        if(is_array($code) && count($code) > 1) {
            $languageList = $languageList->whereIn('code', $code);
        }
        
        $languageList = $languageList->whereIn('store_id', session('adminStoreId'));

        $languageList = $languageList->paginate($limit);
        return $languageList;
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
    public static function getListAll()
    {
        if (self::$getListAll === null) {
            self::$getListAll = self::get()
                ->keyBy('code');
        }
        return self::$getListAll;
    }

    public static function getListActive($storeId)
    {
        if (self::$getListActive === null) {
            $langActive = self::where('status', 1);
            if (!$storeId) {
                $langActive = $langActive->where('store_id',0);
            }else{
                $langActive = $langActive->where('store_id',$storeId);
            }
            $check = $langActive->exists();
            if (!$check) {
                $langActive = $langActive->orWhere('store_id',0);
            }
            $langActive = $langActive->get()->keyBy('code');
            self::$getListActive = $langActive;
        }
        return self::$getListActive;
    }

    public static function getCodeActive($storeId)
    {
        // Print the entire match result

        if (self::$getCodeActive === null) {
            self::$getCodeActive = self::where('status', 1);
            if (strpos($storeId, ',') !== false) {
                $storeId = explode(',',$storeId);
            }else{
                $storeId = (int) $storeId;
            }
            if (!$storeId || $storeId == 'undefined') {
                if ($storeId == 0) {
                    self::$getCodeActive = self::$getCodeActive->where('store_id',$storeId);
                }else{
                    self::$getCodeActive = self::$getCodeActive->whereIn('store_id',session('adminStoreId'));
                }
            }else{
                self::$getCodeActive = self::$getCodeActive->where('store_id',$storeId);
            }
            $check = self::$getCodeActive->exists();
            if (!$check) {
                self::$getCodeActive = self::$getCodeActive->orWhere('store_id',0);
            }
            self::$getCodeActive = self::$getCodeActive->pluck('name', 'code')->all();
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
            if (in_array($model->id, LC_GUARD_LANGUAGE)) {
                return false;
            }
        });
    }
}

<?php
namespace App\Models\Front;

use App\Models\Admin\Config;
use App\Models\Admin\UserStore;
use App\Models\Admin\EmailTemplate;
use Illuminate\Database\Eloquent\Model;
class ShopStore extends Model
{
    public $timestamps = false;
    public $table = 'admin_store';
    protected $guarded = [];
    protected static $getAll = null;
    protected static $getStoreActive = null;
    protected static $getCodeActive = null;
    protected static $getDomainPartner = null;
    protected static $getListAllActive = null;
    protected static $arrayStoreId = null;
    
    public function descriptions()
    {
        return $this->hasMany(ShopStoreDescription::class, 'store_id', 'id');
    }

    public function descriptionsCurrentLang()
    {
        return $this->hasMany(ShopStoreDescription::class, 'store_id', 'id')->where('lang',lc_get_locale());
    }

    public function adminCustomConfig()
    {
        return $this->hasMany(Config::class, 'store_id', 'id')->where('code','admin_config');
    }
    
    public function products()
    {
        return $this->hasMany(ShopProduct::class, 'store_id', 'id');
    }

    public function banners()
    {
        return $this->hasMany(ShopBanner::class, 'store_id', 'id');
    }

    public function languages()
    {
        return $this->hasMany(ShopLanguage::class, 'store_id', 'id');
    }

    public function news()
    {
        return $this->hasMany(ShopNews::class, 'store_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(ShopOrder::class, 'store_id', 'id');
    }

    public function pages()
    {
        return $this->hasMany(ShopPage::class, 'store_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($store) {
            //Store id 1 is default
            if ($store->id == LC_ID_ROOT) {
                return false;
            }
            //Delete store descrition
            $store->descriptions()->delete();
            $store->news()->delete();
            $store->banners()->delete();
            $store->pages()->delete();
            $store->orders()->delete();
            $store->languages()->delete();
            Config::where('store_id', $store->id)->delete();
            UserStore::where('store_id', $store->id)->delete();
            EmailTemplate::where('store_id', $store->id)->delete();
        });
    }


    /**
     * [getAll description]
     *
     * @return  [type]  [return description]
     */
    public static function getListAll()
    {
        if (self::$getAll === null) {
            self::$getAll = self::with('descriptions')
                ->get()
                ->keyBy('id');
        }
        return self::$getAll;
    }

    /**
     * [getAll active description]
     *
     * @return  [type]  [return description]
     */
    public static function getListAllActive()
    {
        if (self::$getListAllActive === null) {
            self::$getListAllActive = self::with('descriptions')
                ->where('active', 1)
                ->get()
                ->keyBy('id');
        }
        return self::$getListAllActive;
    }


    /**
     * Get all domain and id store unlock domain
     *
     * @return  [array]  [return description]
     */
    public static function getDomainPartner()
    {
        if (self::$getDomainPartner === null) {
            self::$getDomainPartner = self::where('partner', 1)
                ->whereNotNull('domain') 
                ->pluck('domain', 'id')
                ->all();
        }
        return self::$getDomainPartner;
    }
    

    /**
     * Get all domain and id store active
     *
     * @return  [array]  [return description]
     */
    public static function getStoreActive()
    {
        if (self::$getStoreActive === null) {
            self::$getStoreActive = self::where('active', 1)
                ->pluck('domain', 'id')
                ->all();
        }
        return self::$getStoreActive;
    }
    

    /**
     * Get all code and id store active
     *
     * @return  [array]  [return description]
     */
    public static function getCodeActive()
    {
        if (self::$getCodeActive === null) {
            self::$getCodeActive = self::where('active', 1)
                ->pluck('code', 'id')
                ->all();
        }
        return self::$getCodeActive;
    }

    /**
     * Get array store ID
     *
     * @return array
     */
    public static function getArrayStoreId()
    {
        if (self::$arrayStoreId === null) {
            self::$arrayStoreId = self::pluck('id')->all();
        }
        return self::$arrayStoreId;
    }

    //Function get text description 
    public function getText() {
        return $this->descriptions()->where('lang', lc_get_locale())->first();
    }
    public function getTitle() {
        return $this->getText()->title;
    }
    public function getDescription() {
        return $this->getText()->description;
    }
    public function getKeyword() {
        return $this->getText()->keyword;
    }
    //End  get text description

}

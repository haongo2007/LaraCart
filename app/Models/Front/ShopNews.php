<?php
namespace App\Models\Front;

use App\Models\Front\ShopNewsDescription;
use Illuminate\Database\Eloquent\Model;
use Cache;
use App\Models\Front\ModelTrait;
class ShopNews extends Model
{
    use ModelTrait;

    public $table = 'shop_news';
    protected $guarded = [];
    protected $connection = LC_CONNECTION;
    protected  $lc_store_id = 0;

    public function descriptions()
    {
        return $this->hasMany(ShopNewsDescription::class, 'news_id', 'id');
    }

    public function descriptionsWithLangDefault()
    {
        return $this->hasOne(ShopNewsDescription::class, 'news_id', 'id')->where('lang', lc_get_locale());
    }
    /*
    Get store
    */
    public function store()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
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
    public function getContent() {
        return $this->getText()->content;
    }
    //End  get text description

    /*
    Get thumb
    */
    public function getThumb()
    {
        return lc_image_get_path_thumb($this->image);
    }

    /*
    Get image
    */
    public function getImage()
    {
        return lc_image_get_path($this->image);

    }
    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl()
    {
        return lc_route('news.detail', ['alias' => $this->alias]);
    }

    //Scort
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }

    public function getBlogList($params){

        $sortBy         = 'sort';
        $sortOrder      = 'asc';
        $filter_sort    = $params['filter_sort'] ?? 'id_desc';
        $filter_keyword = $params['filter_keyword'] ?? '';
        $filter_category    = $params['category'] ?? '';
        $limit              = $params['perPage'] ?? lc_config('news_list');
        $storeId            = $params['storeId'];

        $filterArrSort = [
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];

        if (array_key_exists($filter_sort, $filterArrSort)) {
            $sortBy = $filterArrSort[$filter_sort][0];
            $sortOrder = $filterArrSort[$filter_sort][1];
        }
        $blogs = new self;
        if ($filter_keyword) {
            $blogs = $blogs->setKeyword($filter_keyword);
        }
        if ($filter_category) {
            $categoriId = ShopCategory::select('id')->where('alias',$filter_category)->pluck('id')->first();
            $blogs = $blogs->getProductToCategory($categoriId);
        }

        $blogs = $blogs
            ->setLimit($limit)
            ->setPaginate()
            ->setSort([$sortBy, $sortOrder])
            ->setStore($storeId)
            ->getData();
        return $blogs;
    }
    /**
     * Get news detail
     *
     * @param   [string]  $key     [$key description]
     * @param   [string]  $type  [id, alias]
     *
     */
    public function getDetail($key, $type = null)
    {
        if(empty($key)) {
            return null;
        }
        $tableDescription = (new ShopNewsDescription)->getTable();
        $news = $this
            ->leftJoin($tableDescription, $tableDescription . '.news_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());

        if ($type === null) {
            $news = $news->where('id', (int) $key);
        } else {
            $news = $news->where($type, $key);
        }
        $news = $news->where('status', 1)
            ->where('store_id', config('app.storeId'))
            ->first();
        return $news;
    }

    /**
     * Set store id
     *
     */
    public function setStore($id) {
        $this->lc_store_id = (int)$id;
        return $this;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($news) {
            $news->descriptions()->delete();
            }
        );
    }

    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start() {
        return new ShopNews;
    }

    /**
     * build Query
     */
    public function buildQuery() {
        $tableDescription = (new ShopNewsDescription)->getTable();

        //description
        $query = $this
            ->leftJoin($tableDescription, $tableDescription . '.news_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());
        //search keyword
        if ($this->lc_keyword !='') {
            $query = $query->where(function ($sql) use($tableDescription){
                $sql->where($tableDescription . '.title', 'like', '%' . $this->lc_keyword . '%')
                ->orWhere($tableDescription . '.keyword', 'like', '%' . $this->lc_keyword . '%')
                ->orWhere($tableDescription . '.description', 'like', '%' . $this->lc_keyword . '%');
            });
        }
        
        $storeId = $this->lc_store_id ? $this->lc_store_id : config('app.storeId');
        //Process store
        if (!empty($this->lc_store_id) || config('app.storeId') != 1) {
            //If the store is specified or the default is not the primary store
            //Only get products from eligible stores
            $query = $query->where($this->getTable().'.store_id', $storeId);
        }
        //End store

        $query = $query->where('status', 1)
        ->where('store_id', config('app.storeId'));

        if (count($this->lc_moreWhere)) {
            foreach ($this->lc_moreWhere as $key => $where) {
                if(count($where)) {
                    $query = $query->where($where[0], $where[1], $where[2]);
                }
            }
        }

        if ($this->lc_random) {
            $query = $query->inRandomOrder();
        } else {
            $ckeckSort = false;
            if (is_array($this->lc_sort) && count($this->lc_sort)) {
                foreach ($this->lc_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        if ($rowSort[0] == 'sort') {
                            $ckeckSort = true;
                        }
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }
        //Use field "sort" if haven't above
        if (!$ckeckSort) {
            $query = $query->orderBy($this->getTable().'.sort', 'asc');
        }
        //Default, will sort id
        $query = $query->orderBy($this->getTable().'.id', 'desc');

        return $query;
    }

}

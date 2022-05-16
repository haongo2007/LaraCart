<?php
namespace App\Models\Admin;

use App\Models\Admin\StoreCategoryDescription;
use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ModelTrait;

class StoreCategory extends Model
{
    use ModelTrait;

    public $timestamps = false;
    public $table = 'store_category';
    protected $guarded = [];
    protected $connection = LC_CONNECTION;

    protected  $lc_parent = ''; // category id parent

    public function descriptions()
    {
        return $this->hasMany(StoreCategoryDescription::class, 'category_id', 'id');
    }
    public function Parent()
    {
        return $this->hasOne(self::class,'id','parent');
    }
    public function Children()
    {
        return $this->hasMany(self::class,'parent');
    }
    public function descriptionsWithLangDefault()
    {
        return $this->hasOne(StoreCategoryDescription::class, 'category_id', 'id')->where('lang', lc_get_locale());
    }

    /**
     * Get category detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCategoryAdmin($id) {
        return self::where('id', $id)
        ->first();
    }
/**
 * Get category parent
 * @return [type]     [description]
 */
    public function getParent()
    {
        return $this->getDetail($this->parent);

    }

     /**
     * Get list category cms
     *
     * @param   array  $arrOpt
     * Example: ['status' => 1, 'top' => 1]
     * @param   array  $arrSort
     * Example: ['sortBy' => 'id', 'sortOrder' => 'asc']
     * @param   array  $arrLimit  [$arrLimit description]
     * Example: ['step' => 0, 'limit' => 20]
     * @return  [type]             [return description]
     */
    public function getList($arrOpt = [], $arrSort = [], $arrLimit = [])
    {
        $sortBy = $arrSort['sortBy'] ?? null;
        $sortOrder = $arrSort['sortOrder'] ?? 'asc';
        $step = $arrLimit['step'] ?? 0;
        $limit = $arrLimit['limit'] ?? 0;

        $tableDescription = (new StoreCategoryDescription)->getTable();

        //description
        $data = $this
            ->leftJoin($tableDescription, $tableDescription . '.category_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());

        $data = $data->sort($sortBy, $sortOrder);
        if(count($arrOpt = [])) {
            foreach ($arrOpt as $key => $value) {
                $data = $data->where($key, $value);
            }
        }
        if((int)$limit) {
            $start = $step * $limit;
            $data = $data->offset((int)$start)->limit((int)$limit);
        }
        $data = $data->get()->groupBy('parent');

        return $data;
    }


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

    public function getUrl()
    {
        return lc_route('cms.category', ['alias' => $this->alias]);
    }


    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($category) {
            //Delete category descrition
            $category->descriptions()->delete();
        });
    }

//Scort
    public function scopeSort($query, $column = null)
    {
        $column = $column ?? 'sort';
        return $query->orderBy($column, 'asc')->orderBy('id', 'desc');
    }

    /**
     * Get categoy detail
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

        $tableDescription = (new StoreCategoryDescription)->getTable();

        //description
        $category = $this
            ->leftJoin($tableDescription, $tableDescription . '.category_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', lc_get_locale())
            ->where($this->getTable() . '.store_id', config('app.storeId'));

        if ($type == null) {
            $category = $category->where('id', (int) $key);
        } else {
            $category = $category->where($type, $key);
        }
        $category = $category->where('status', 1);

        return $category->first();
    }


    /**
     * Set category parent
     */
    public function setParent($parent) {
        if ($parent === 'all') {
            $this->lc_parent = $parent;
        } else {
            $this->lc_parent = (int)$parent;
        }
        return $this;
    }

    /**
     * Category root
     */
    public function getCategoryRoot() {
        $this->setParent(0);
        return $this;
    }

    /**
     * Create a new category
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createCategoryAdmin(array $dataInsert) {

        return self::create($dataInsert);
    }
    
    /**
     * Insert data description
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function insertDescriptionAdmin(array $dataInsert) {

        return StoreCategoryDescription::create($dataInsert);
    }

    /**
     * build Query
     */
    public function buildQuery() {
        $tableDescription = (new StoreCategoryDescription)->getTable();

        //description
        $query = $this
            ->leftJoin($tableDescription, $tableDescription . '.category_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());
        //search keyword
        if ($this->lc_keyword !='') {
            $query = $query->where(function ($sql) use($tableDescription){
                $sql->where($tableDescription . '.title', 'like', '%' . $this->lc_keyword . '%');
            });
        }

        $query = $query->where('status', 1)
        ->where('store_id', config('app.storeId'));

        if ($this->lc_parent !== 'all') {
            $query = $query->where('parent', $this->lc_parent);
        }

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
            if (is_array($this->lc_sort) && count($this->lc_sort)) {
                foreach ($this->lc_sort as  $rowSort) {
                    if(is_array($rowSort) && count($rowSort) == 2) {
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }

        return $query;
    }
}

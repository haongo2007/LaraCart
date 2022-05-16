<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StoreCategoryDescription extends Model
{
    protected $primaryKey = ['lang', 'category_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'store_category_description';
    protected $connection = LC_CONNECTION;
}

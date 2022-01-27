<?php
#app/Models/AdminUserStore.php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class UserStore extends Model
{
    protected $primaryKey = ['store_id', 'user_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'admin_user_store';
    protected $connection = LC_CONNECTION;
}

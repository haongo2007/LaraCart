<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class Role extends Model
{
    const ITEM_PER_PAGE = 15;
    protected $fillable = ['name', 'slug'];
    public $table       = 'admin_role';

    public function administrators()
    {

        return $this->belongsToMany(User::class, 'admin_role_user', 'role_id', 'user_id');
    }

    /**
     * A role belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'admin_role_permission', 'role_id', 'permission_id');
    }

    /**
     * A role belongs to many menus.
     *
     * @return BelongsToMany
     */
    public function menus()
    {

        return $this->belongsToMany(Menu::class, 'admin_role_menu', 'role_id', 'menu_id');
    }

    /**
     * Check user has permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function can(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function cannot(string $permission): bool
    {
        return !$this->can($permission);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->administrators()->detach();
            // $model->menus()->detach();
            $model->permissions()->detach();
        });
    }

    /**
     * Update info customer
     * @param  [array] $dataUpdate
     * @param  [int] $id
     */
    public static function updateInfo($dataUpdate, $id)
    {
        $dataUpdate = bc_clean($dataUpdate, 'password');
        $obj        = self::find($id);
        return $obj->update($dataUpdate);
    }

    /**
     * Create new role
     * @return [type] [description]
     */
    public static function createRole($dataInsert)
    {
        $dataUpdate = lc_clean($dataInsert, 'password');
        return self::create($dataUpdate);
    }

    public function getRolesListAdmin(array $dataSearch)
    {

        $limit = Arr::get($dataSearch, 'limit', self::ITEM_PER_PAGE);
        $contain = Arr::get($dataSearch, 'contain', '');

        $rolesList     = (new self);

        if ($contain) {
            $rolesList = $rolesList->where(function ($sql) use ($contain) {
                $sql->where('name', 'like', '%' . $contain . '%');
            });
        }

        if ($limit == 'all') {
            return $rolesList->get();
        }
        $rolesList = $rolesList->paginate($limit);

        return $rolesList;
    }
}

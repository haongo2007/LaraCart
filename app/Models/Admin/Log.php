<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];
    public $table = BC_DB_PREFIX.'admin_log';
    public static $methodColors = [
        'GET' => 'green',
        'POST' => 'yellow',
        'PUT' => 'blue',
        'DELETE' => 'red',
    ];

    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH',
        'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE',
    ];

    /**
     * Log belongs to users.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(AdminUser::class);
    }
}

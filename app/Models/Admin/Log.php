<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];
    public $table = 'admin_log';
    const ITEM_PER_PAGE = 15;

    /**
     * Log belongs to users.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getLogsListAdmin($searchParams)
    {
        $sort_order = $searchParams['sort_order'] ?? 'id_desc';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        $arrSort = [
            'id__desc' => trans('log.admin.sort_order.id_desc'),
            'id__asc' => trans('log.admin.sort_order.id_asc'),
            'user_id__desc' => trans('log.admin.sort_order.user_id_desc'),
            'user_id__asc' => trans('log.admin.sort_order.user_id_asc'),
            'path__desc' => trans('log.admin.sort_order.path_desc'),
            'path__asc' => trans('log.admin.sort_order.path_asc'),
            'user_agent__desc' => trans('log.admin.sort_order.user_agent_desc'),
            'user_agent__asc' => trans('log.admin.sort_order.user_agent_asc'),
            'method__desc' => trans('log.admin.sort_order.method_desc'),
            'method__asc' => trans('log.admin.sort_order.method_asc'),
            'ip__desc' => trans('log.admin.sort_order.ip_desc'),
            'ip__asc' => trans('log.admin.sort_order.ip_asc'),

        ];
        $Logs = new Log;

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $Logs = $Logs->orderBy($field, $sort_field);

        } else {
            $Logs = $Logs->orderBy('id', 'desc');
        }
       return $Logs->paginate($limit);
    }
}

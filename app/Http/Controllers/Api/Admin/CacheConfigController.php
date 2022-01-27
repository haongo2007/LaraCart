<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\RootAdminController;
use BlackCart\Core\Admin\Models\AdminConfig;
class CacheConfigController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = [
            'title' => trans('cache.config_manager.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',        ];
        $configs = AdminConfig::getListConfigByCode(['code' => 'cache']);
        $data['configs'] = $configs;
        $data['urlUpdateConfigGlobal'] = bc_route_admin('admin_config_global.update');
        return view($this->templatePathAdmin.'screen.cache_config')
            ->with($data);
    }

    /**
     * Clear cache
     *
     * @return  json
     */
    public function clearCache() {
        $action = request('action');
        $response = bc_clear_cache($action);
        return response()->json(
            $response
        );
    }
}

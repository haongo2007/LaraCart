<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\RootAdminController;

class EnvConfigController extends RootAdminController
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {

        $data = [
            'title'    => trans('env.title'),
            'subTitle' => '',
            'icon'     => 'fa fa-indent',
        ];
        $data['urlUpdateConfigGlobal'] = bc_route_admin('admin_config_global.update');
        return view($this->templatePathAdmin.'screen.env')
            ->with($data);
    }

}

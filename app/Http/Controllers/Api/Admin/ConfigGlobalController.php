<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Config;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

class ConfigGlobalController extends Controller
{
    public $templates, $currencies, $languages, $timezones;

    public function __construct()
    {
    }

    public function webhook()
    {
        $data = [
            'title' => trans('config.admin.webhook'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',  
        ];
        return view($this->templatePathAdmin.'screen.webhook')
            ->with($data);
    }

    /**
     * Update config global
     *
     * @return  [type]  [return description]
     */
    public function update() {
        $data = request()->all();
        $name = $data['name'];
        $value = $data['value'];
        try {
            Config::where('key', $name)
                ->where('store_id', 0)
                ->update(['value' => $value]);
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            return response()->json(new JsonResponse([], $msg), Response::HTTP_FORBIDDEN);
        }
        
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}

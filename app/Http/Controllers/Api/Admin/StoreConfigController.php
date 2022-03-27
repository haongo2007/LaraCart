<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Front\ShopCurrency;
use App\Models\Admin\Config;
use App\Models\Admin\Store;
use App\Models\Front\ShopTax;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\StoreCollection;

class StoreConfigController extends Controller
{
    public $templates, $currencies, $languages, $timezones;

    public function __construct()
    {
        // parent::__construct();
        $allTemplate = lc_get_all_template();
        $templates = [];
        foreach ($allTemplate as $key => $template) {
            $templates[$key] = empty($template['config']['name']) ? $key : $template['config']['name'];
        }
        foreach (timezone_identifiers_list() as $key => $value) {
            $timezones[$value] = $value;
        }
        $this->templates = $templates;
        $this->currencies = ShopCurrency::getCodeActive();
        $this->languages = ShopLanguage::getListActive();
        $this->timezones = $timezones;

    }

    public function index() {
        $searchParams = request()->all();
        $data = (new Store)->getStoreListAdmin($searchParams);
        return StoreCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    public function show($id)
    {
        
        $store = (new Store)->with('descriptions')->find($id);
        if (!$store) {
            return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new JsonResponse($store), Response::HTTP_OK);
    }
    /*
    Update value config store
    */
    public function update()
    {
        $data = request()->all();
        $name = $data['name'];
        $value = $data['value'];
        $storeId = $data['storeId'] ?? '';
        if (!$storeId) {
            return response()->json([
                'error' => 1,
                'field' => 'storeId',
                'value' => $storeId,
                'msg'   => 'Store ID can not empty!',
                ]
            );
        }

        try {
            Config::where('key', $name)
                ->where('store_id', $storeId)
                ->update(['value' => $value]);
            $error = 0;
            $msg = trans('admin.update_success');
        } catch (\Throwable $e) {
            $error = 1;
            $msg = $e->getMessage();
        }
        return response()->json([
            'error' => $error,
            'field' => $name,
            'value' => $value,
            'msg'   => $msg,
            ]
        );

    }

}

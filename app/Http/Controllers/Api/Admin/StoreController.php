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
use Illuminate\Http\Request;
use App\Http\Resources\StoreCollection;

class StoreController extends Controller
{
    public $currencies, $languages, $timezones;

    public function __construct()
    {
        foreach (timezone_identifiers_list() as $key => $value) {
            $timezones[$value] = $value;
        }
        $this->currencies = ShopCurrency::getCodeActive();
        $this->languages = ShopLanguage::getListActive();
        $this->timezones = $timezones;
    }

    public function index() {
        $searchParams = request()->all();
        $data = (new Store)->getStoreListAdmin($searchParams);
        return StoreCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    public function show($id) {
        if ($id) {   
            $store = (new Store)->with('descriptions')->find($id);
            if (!$store) {
                return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
            }
        }
        $store['timezones'] = $this->timezones;
        $store['languages'] = $this->languages;
        $store['currencies'] =$this->currencies;
        return response()->json(new JsonResponse($store), Response::HTTP_OK);
    }
    
    public function store(Request $request)
    {
        $data = array_filter($request->all());
        $data['status'] = 1;
        $data['active'] = 0;
        $descriptions = $data['descriptions'];
        if (array_key_exists('time_active', $data)) {
            $data['time_active'] = json_encode($data['time_active']);
        }
        unset($data['descriptions']);
        $stored = Store::create($data);
        foreach ($descriptions as $key => $value) {
            $descriptions[$key]['description'] = $value['description']['value'];
            $descriptions[$key]['title'] = $value['title']['value'];
            $descriptions[$key]['keyword'] = $value['keyword']['value'];
            $descriptions[$key]['store_id'] = $stored->id;
        }
        Store::insertDescription($descriptions);
        $seeder = new \Database\Seeders\DataStoreSeeder();
        $seeder->run($stored->id);
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }

    public function update(Request $request,$id)
    {
        $store = Store::find($id);
        if(!$store){
            return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if (array_key_exists('time_active', $data)) {
            $data['time_active'] = json_encode($data['time_active']);
        }
        if (array_key_exists('descriptions', $data)) {
            $descriptions = $data['descriptions'];
            unset($data['descriptions']);
        }
        $data = array_filter($data,'strlen');
        if (isset($descriptions)) {
            foreach ($descriptions as $key => $value) {
                $descriptions[$key]['description'] = $value['description']['value'];
                $descriptions[$key]['title'] = $value['title']['value'];
                $descriptions[$key]['keyword'] = $value['keyword']['value'];
                $descriptions[$key]['store_id'] = $value['store_id'];
            }
            $store->descriptions()->delete();
            Store::insertDescription($descriptions);
        }
        if (!empty($data)) {
            $store->update($data);
        }
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }
    public function destroy($id)
    {
        $check = Store::find($id);
        if (!$check) {
            return response()->json(new JsonResponse([],trans('admin.permission_denied')) ,Response::HTTP_FORBIDDEN);
        } else {
            $check->delete();
            return response()->json(new JsonResponse([]),Response::HTTP_OK);
        }
    }
}

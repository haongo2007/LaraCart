<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\LanguageCollection;
use Session;
use Validator;

class LanguageController extends Controller
{
    public function index()
    {
        $searchParams = request()->all();
        $data = (new ShopLanguage)->getLanguageListAdmin($searchParams);
        if ($data->total() == 0) {
            $data = lc_language_default();
        }
        return LanguageCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Get active languages
     * @return [type] [description]
     */
    public function getActiveLanguages($storeId)
    {
        $languages = (new ShopLanguage)->getCodeActive($storeId);
        return response()->json(new JsonResponse($languages), Response::HTTP_OK);
    }
    
/**
 * Post create
 * @return [type] [description]
 */
    public function store()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'sort' => 'numeric|min:0',
            'status' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopLanguage::class.'",code',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'icon' => $data['icon'],
            'name' => $data['name'],
            'code' => strtolower($data['code']),
            'rtl' => empty($data['rtl']) ? 0 : 1,
            'status' => empty($data['status']) ? 0 : 1,
            'sort' => (int) $data['sort'],
        ];
        $lang = ShopLanguage::create($dataInsert);
        return response()->json(new JsonResponse(['id'=>$lang->id]), Response::HTTP_OK);

    }

    /**
    * update
    */
    public function update($id)
    {
        $language = ShopLanguage::find($id);
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'icon' => 'required',
            'name' => 'required',
            'sort' => 'numeric|min:0',
            'code' => 'required|unique:"'.ShopLanguage::class.'",code,' . $language->id . ',id',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataUpdate = [
            'icon' => $data['icon'],
            'name' => $data['name'],
            'code' => $data['code'],
            'rtl' => empty($data['rtl']) ? 0 : 1,
            'status' => empty($data['status']) ? 0 : 1,
            'sort' => $data['sort'],
        ];
        $lang = ShopLanguage::find($id);
        if (!$lang) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $lang->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        $arrID = array_diff($arrID, LC_GUARD_LANGUAGE);
        ShopLanguage::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    public function changeLanguages($lang)
    {
        //Set language
        $languages = ShopLanguage::where('code',$lang)->first();
        if (!$languages) {
            return response()->json(new JsonResponse([], 'Languages Do not exist'), Response::HTTP_FORBIDDEN);
        }
        if (!Session::has('locale')) {
            $detectLocale = lc_store('language') ?? config('app.locale');
        } else {
            $detectLocale = session('locale');
        }
        session(['locale' => $lang]);
        app()->setLocale($lang);
        //End language
        return response()->json(new JsonResponse($languages,'Successfully' ), Response::HTTP_OK);

    }

}

<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Validator;
use App\Models\Admin\Banner;
use App\Models\Front\ShopBannerType;
use App\Http\Resources\BannerCollection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Helper\JsonResponse;

class BannerController extends Controller
{
    protected $arrTarget;
    protected $dataType;
    public function __construct()
    {
        // $this->arrTarget = ['_blank' => '_blank', '_self' => '_self'];
        // $this->dataType  = (new ShopBannerType)->pluck('name', 'code')->all();
        // if(lc_config_global('MultiVendorPro')) {
        //     $this->dataType['background-store'] = 'Background store';
        //     $this->dataType['breadcrumb-store'] = 'Breadcrumb store';
        // }
        // ksort($this->dataType);
    }

    public function index()
    {
        $dataSearch = request()->all();
        $data = Banner::getBannerListAdmin($dataSearch);
        return BannerCollection::collection($data)->additional(['message' => 'Successfully']);
    }


    /**
     * Post create new item in admin
     * @return [type] [description]
    */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'sort' => 'numeric|min:0',
            'title' => 'required',
            'store_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $dataInsert = [
            'image'    => $data['image'] ? $data['image'][0] : null,
            'url'      => $data['url'] ?? null,
            'title'    => $data['title'],
            'html'     => $data['html'] ?? null,
            'type'     => $data['type'] ?? 0,
            'target'   => $data['target'],
            'status'   => empty($data['status']) ? 0 : 1,
            'sort'     => (int) $data['sort'],
            'store_id' => $data['store_id'],
        ];
        Banner::createBannerAdmin($dataInsert);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    /*
     * update status
     */
    public function update(Request $request,$id)
    {
        $banner = Banner::getBannerAdmin($id);
        if (!$banner) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'sort' => 'numeric|min:0',
            'title' => 'required',
            'store_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        //Edit
        $dataUpdate = [
            'image'    => is_array($data['image']) ? $data['image'][0] : $data['image'],
            'url'      => $data['url'],
            'title'    => $data['title'],
            'html'     => $data['html'],
            'type'     => $data['type'] ?? 0,
            'target'   => $data['target'],
            'status'   => empty($data['status']) ? 0 : 1,
            'sort'     => (int) $data['sort'],
            'store_id' => $data['store_id'],

        ];
        $banner->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        Banner::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}

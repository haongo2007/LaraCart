<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Validator;
use App\Models\Admin\Banner;
use App\Models\Front\ShopBannerType;
use App\Http\Resources\BannerCollection;

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
 * Form create new item in admin
 * @return [type] [description]
 */
    public function create()
    {
        $data = [
            'title' => trans('banner.admin.add_new_title'),
            'title_description' => trans('banner.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'banner' => [],
            'arrTarget' => $this->arrTarget,
            'dataType' => $this->dataType,
            'url_action' => bc_route_admin('admin_banner.create'),
        ];
        return view($this->templatePathAdmin.'Banner.add_edit')
            ->with($data);
    }

/**
 * Post create new item in admin
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'sort' => 'numeric|min:0',
            'email' => 'email|nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $dataInsert = [
            'image'    => $data['image'],
            'url'      => $data['url'],
            'title'    => $data['title'],
            'html'     => $data['html'],
            'type'     => $data['type'] ?? 0,
            'target'   => $data['target'],
            'status'   => empty($data['status']) ? 0 : 1,
            'sort'     => (int) $data['sort'],
            'store_id' => session('adminStoreId'),
        ];
        AdminBanner::createBannerAdmin($dataInsert);
        return redirect()->route('admin_banner.index')->with('success', trans('banner.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id)
    {
        $banner = AdminBanner::getBannerAdmin($id);

        if (!$banner) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = [
            'title'             => trans('banner.admin.edit'),
            'arrTarget'         => $this->arrTarget,
            'dataType'          => $this->dataType,
            'banner'            => $banner,
            'url_action'        => bc_route_admin('admin_banner.edit', ['id' => $banner['id']]),
        ];
        return view($this->templatePathAdmin.'Banner.add_edit')
            ->with($data);
    }

    /*
     * update status
     */
    public function postEdit($id)
    {
        $banner = AdminBanner::getBannerAdmin($id);
        if (!$banner) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'sort' => 'numeric|min:0',
            'email' => 'email|nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $dataUpdate = [
            'image'    => $data['image'],
            'url'      => $data['url'],
            'title'    => $data['title'],
            'html'     => $data['html'],
            'type'     => $data['type'] ?? 0,
            'target'   => $data['target'],
            'status'   => empty($data['status']) ? 0 : 1,
            'sort'     => (int) $data['sort'],
            'store_id' => session('adminStoreId'),

        ];
        $banner->update($dataUpdate);

        return redirect()->route('admin_banner.index')->with('success', trans('banner.admin.edit_success'));

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => trans('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if(!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(['error' => 1, 'msg' => trans('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
            }

            AdminBanner::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminBanner::getBannerAdmin($id);
    }

}

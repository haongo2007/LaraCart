<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin\Discount;
use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Front\ShopDiscount;
use App\Http\Resources\DiscountCollection;
use Validator;

class DiscountController extends Controller
{
    public $plugin;

    public function __construct()
    {
        // $this->languages = ShopLanguage::getListActive();
        // $this->plugin = new AppConfig;
    }

    public function index()
    {
        // (new ShopDiscount)->install();
        $dataSearch = request()->all();
        $data = (new Discount)->getDiscountListAdmin($dataSearch);
        return DiscountCollection::collection($data)->additional(['message' => 'Successfully']);
    }

/**
 * Form create new
 * @return [type] [description]
 */
    public function create()
    {
        $data = [
            'title' => trans($this->plugin->pathPlugin.'::lang.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => trans($this->plugin->pathPlugin.'::lang.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'discount' => [],
            'url_action' => bc_route_admin('admin_discount.create'),
        ];
        return view($this->plugin->pathPlugin.'::Admin')
            ->with($data);
    }

/**
 * Post create new 
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'code'   => 'required|regex:/(^([0-9A-Za-z\-\._]+)$)/|discount_unique|string|max:50',
            'limit'  => 'required|numeric|min:1',
            'reward' => 'required|numeric|min:0',
            'type'   => 'required',
        ], [
            'code.regex' => trans($this->plugin->pathPlugin.'::lang.admin.code_validate'),
            'code.discount_unique' => trans($this->plugin->pathPlugin.'::lang.discount_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $dataInsert = [
            'code'       => $data['code'],
            'reward'     => (int)$data['reward'],
            'limit'      => $data['limit'],
            'type'       => $data['type'],
            'data'       => $data['data'],
            'login'      => empty($data['login']) ? 0 : 1,
            'status'     => empty($data['status']) ? 0 : 1,
            'store_id'   => session('adminStoreId'),
        ];
        if(!empty($data['expires_at'])) {
            $dataInsert['expires_at'] = $data['expires_at'];
        }
        AdminDiscount::createDiscountAdmin($dataInsert);

        return redirect()->route('admin_discount.index')->with('success', trans($this->plugin->pathPlugin.'::lang.admin.create_success'));

    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $discount = AdminDiscount::getDiscountAdmin($id);
        if (!$discount) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = [
            'title'             => trans($this->plugin->pathPlugin.'::lang.admin.edit'),
            'subTitle'          => '',
            'title_description' => '',
            'icon'              => 'fa fa-pencil-square-o',
            'discount'          => $discount,
            'url_action'        => bc_route_admin('admin_discount.edit', ['id' => $discount['id']]),
        ];
        return view($this->plugin->pathPlugin.'::Admin')
            ->with($data);
    }

    /**
     * update
     */
    public function postEdit($id)
    {
        $discount = AdminDiscount::getDiscountAdmin($id);
        if (!$discount) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = request()->all();
        $validator = Validator::make($data, [
            'code' => 'required|regex:/(^([0-9A-Za-z\-\._]+)$)/|discount_unique:' . $discount->id . '|string|max:50',
            'limit' => 'required|numeric|min:1',
            'reward' => 'required|numeric|min:0',
            'type' => 'required',
        ], [
            'code.regex' => trans($this->plugin->pathPlugin.'::lang.admin.code_validate'),
            'code.discount_unique' => trans($this->plugin->pathPlugin.'::lang.discount_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $dataUpdate = [
            'code'       => $data['code'],
            'reward'     => (int)$data['reward'],
            'limit'      => $data['limit'],
            'type'       => $data['type'],
            'data'       => $data['data'],
            'login'      => empty($data['login']) ? 0 : 1,
            'status'     => empty($data['status']) ? 0 : 1,
            'store_id'   => session('adminStoreId'),
        ];
        if(!empty($data['expires_at'])) {
            $dataUpdate['expires_at'] = $data['expires_at'];
        }
        $discount->update($dataUpdate);

        return redirect()->route('admin_discount.index')
            ->with('success', trans($this->plugin->pathPlugin.'::lang.admin.edit_success'));

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return 0;
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
            AdminDiscount::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminDiscount::getDiscountAdmin($id);
    }
}

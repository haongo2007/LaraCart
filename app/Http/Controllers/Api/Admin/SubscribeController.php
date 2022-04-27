<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Subscribe;
use Validator;
use App\Http\Resources\SubscribeCollection;

class SubscribeController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $dataSearch = request()->all();
        $data = Subscribe::getSubscribeListAdmin($dataSearch);
        return SubscribeCollection::collection($data)->additional(['message' => 'Successfully']);
    }

/**
 * Form create new item in admin
 * @return [type] [description]
 */
    public function create()
    {
        $data = [
            'title' => trans('subscribe.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => trans('subscribe.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'subscribe' => [],
            'url_action' => bc_route_admin('admin_subscribe.create'),
        ];
        return view($this->templatePathAdmin.'Subcribe.add_edit')
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
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataInsert = [
            'email' => $data['email'],
            'status' => (!empty($data['status']) ? 1 : 0),
            'store_id' => session('adminStoreId'),
        ];
        AdminSubscribe::createSubscribeAdmin($dataInsert);

        return redirect()->route('admin_subscribe.index')->with('success', trans('subscribe.admin.create_success'));

    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $subscribe = AdminSubscribe::getSubscribeAdmin($id);

        if (!$subscribe) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = [
            'title' => trans('subscribe.admin.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'subscribe' => $subscribe,
            'url_action' => bc_route_admin('admin_subscribe.edit', ['id' => $subscribe['id']]),
        ];
        return view($this->templatePathAdmin.'Subcribe.add_edit')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $subscribe = AdminSubscribe::getSubscribeAdmin($id);
        if (!$subscribe) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    //Edit

        $dataUpdate = [
            'email' => $data['email'],
            'status' => (!empty($data['status']) ? 1 : 0),
            'store_id' => session('adminStoreId'),

        ];
        $subscribe->update($dataUpdate);

        return redirect()->route('admin_subscribe.index')
                ->with('success', trans('subscribe.admin.edit_success'));

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
            AdminSubscribe::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminSubscribe::getSubscribeAdmin($id);
    }

}

<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BannerType;
use App\Http\Resources\BannerTypeCollection;
use Validator;

class BannerTypeController extends Controller
{
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $dataSearch = request()->all();
        $data = BannerType::getBannerTypeListAdmin($dataSearch);
        return BannerTypeCollection::collection($data)->additional(['message' => 'Successfully']);
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
            'name' => 'required',
            'code' => 'required|unique:"'.BannerType::class.'",code',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            // dd($validator->messages());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data['code'] = bc_word_format_url($data['code']);
        $data['code'] = bc_word_limit($data['code'], 100);
        $dataInsert = [
            'code' => $data['code'],
            'name' => $data['name'],
        ];
        $obj = BannerType::create($dataInsert);
//
        return redirect()->route('admin_banner_type.index')->with('success', trans('banner_type.admin.create_success'));

    }

/**
 * Form edit
 */
public function edit($id)
{
    $banner_type = BannerType::find($id);
    if(!$banner_type) {
        return 'No data';
    }
    $data = [
        'title' => trans('banner_type.admin.list'),
        'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . trans('banner_type.admin.edit'),
        'subTitle' => '',
        'icon' => 'fa fa-indent',
        'urlDeleteItem' => bc_route_admin('admin_banner_type.delete'),
        'removeList' => 0, // 1 - Enable function delete list item
        'buttonRefresh' => 0, // 1 - Enable button refresh
        'buttonSort' => 0, // 1 - Enable button sort
        'css' => '', 
        'js' => '',
        'url_action' => bc_route_admin('admin_banner_type.edit', ['id' => $banner_type['id']]),
        'banner_type' => $banner_type,
        'id' => $id,
    ];

    $listTh = [
        'id' => trans('banner_type.admin.id'),
        'code' => trans('banner_type.admin.code'),
        'name' => trans('banner_type.admin.name'),
        'action' => trans('banner_type.admin.action'),
    ];
    $obj = new BannerType;
    $obj = $obj->orderBy('id', 'desc');
    $dataTmp = $obj->paginate(20);

    $dataTr = [];
    foreach ($dataTmp as $key => $row) {
        $dataTr[] = [
            'id' => $row['id'],
            'code' => $row['code'] ?? 'N/A',
            'name' => $row['name'] ?? 'N/A',
            'action' => '
                <a href="' . bc_route_admin('admin_banner_type.edit', ['id' => $row['id']]) . '"><span title="' . trans('banner_type.admin.edit') . '" type="button" class="btn btn-flat btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

              <span onclick="deleteItem(' . $row['id'] . ');"  title="' . trans('banner_type.admin.delete') . '" class="btn btn-flat btn-danger"><i class="fas fa-trash-alt"></i></span>
              ',
        ];
    }

    $data['listTh'] = $listTh;
    $data['dataTr'] = $dataTr;
    $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'Component.pagination');
    $data['resultItems'] = trans('banner_type.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'item_total' => $dataTmp->total()]);

    $data['layout'] = 'edit';
    return view($this->templatePathAdmin.'screen.banner_type')
        ->with($data);
}

/**
 * update status
 */
    public function postEdit($id)
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $obj = BannerType::find($id);
        $validator = Validator::make($dataOrigin, [
            'code' => 'required|unique:"'.BannerType::class.'",code,' . $obj->id . ',id',
            'name' => 'required',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            // dd($validator->messages());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $data['code'] = bc_word_format_url($data['code']);
        $data['code'] = bc_word_limit($data['code'], 100);
        $dataUpdate = [
            'code' => $data['code'],
            'name' => $data['name'],
        ];
        $obj->update($dataUpdate);
//
        return redirect()->back()->with('success', trans('banner_type.admin.edit_success'));

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
            BannerType::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}

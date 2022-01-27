<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLength;
use App\Http\Resources\LengthCollection;
use Validator;

class LengthController extends Controller
{

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $data = ShopLength::orderBy('id', 'desc')->paginate(20);
        return LengthCollection::collection($data)->additional(['message' => 'Successfully']);
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
            'name' => 'required|unique:"'.ShopLength::class.'",name',
            'description' => 'required',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            // dd($validator->messages());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
//Create new order
        $dataInsert = [
            'name' => $data['name'],
            'description' => $data['description'],
        ];
        $obj = ShopLength::create($dataInsert);
//
        return redirect()->route('admin_length_unit.index')->with('success', trans('length.admin.create_success'));

    }

    /**
     * Form edit
     */

    public function edit($id)
    {
        $length = ShopLength::find($id);
        if(!$length) {
            return 'No data';
        }
        $data = [
            'title' => trans('length.admin.list'),
            'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . trans('length.admin.edit'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => bc_route_admin('admin_length_unit.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '', 
            'js' => '',
            'url_action' => bc_route_admin('admin_length_unit.edit', ['id' => $length['id']]),
            'length' => $length,
            'id' => $id,
        ];

        $listTh = [
            'id' => trans('length.id'),
            'name' => trans('length.name'),
            'description' => trans('length.description'),
            'action' => trans('length.admin.action'),
        ];
        $obj = new ShopLength;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'action' => '
                    <a href="' . bc_route_admin('admin_length_unit.edit', ['id' => $row['id']]) . '"><span title="' . trans('length.admin.edit') . '" type="button" class="btn btn-flat btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                <span onclick="deleteItem(' . $row['id'] . ');"  title="' . trans('length.admin.delete') . '" class="btn btn-flat btn-danger"><i class="fas fa-trash-alt"></i></span>
                ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'Component.pagination');
        $data['resultItems'] = trans('length.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'item_total' => $dataTmp->total()]);

        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.length')
            ->with($data);
    }


/**
 * update status
 */
    public function postEdit($id)
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $obj = ShopLength::find($id);
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|unique:"'.ShopLength::class.'",name,' . $obj->id . ',id',
            'description' => 'required',
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
        $dataUpdate = [
            'name' => $data['name'],
            'description' => $data['description'],
        ];
        $obj->update($dataUpdate);

//
        return redirect()->back()->with('success', trans('length.admin.edit_success'));

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
            ShopLength::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}

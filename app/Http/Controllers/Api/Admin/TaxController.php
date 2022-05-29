<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopTax;
use App\Http\Resources\TaxCollection;
use Validator;

class TaxController extends Controller
{
    public function index()
    {
        $data = (new ShopTax)->getTaxListAdmin(request()->all());
        return TaxCollection::collection($data)->additional(['message' => 'Successfully']);
    }


/**
 * Post create new item in admin
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'value' => 'numeric|min:0',
        ],[
            'name.required' => trans('validation.required', ['attribute' => trans('tax.name')]),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }

        $dataInsert = [
            'value' => (int)$data['value'],
            'name' => $data['name'],
        ];
        $obj = ShopTax::create($dataInsert);

        return redirect()->route('admin_tax.index')->with('success', trans('tax.admin.create_success'));

    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $tax = ShopTax::find($id);
        if(!$tax) {
            return 'No data';
        }
        $data = [
            'title' => trans('tax.admin.list'),
            'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . trans('tax.admin.edit'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => bc_route_admin('admin_tax.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '', 
            'js' => '',
            'url_action' => bc_route_admin('admin_tax.edit', ['id' => $tax['id']]),
            'tax' => $tax,
            'id' => $id,
        ];

        $listTh = [
            'id' => trans('tax.id'),
            'name' => trans('tax.name'),
            'value' => trans('tax.value'),
            'action' => trans('tax.admin.action'),
        ];
        $obj = new ShopTax;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'value' => $row['value'],
                'action' => '
                    <a href="' . bc_route_admin('admin_tax.edit', ['id' => $row['id']]) . '"><span title="' . trans('tax.admin.edit') . '" type="button" class="btn btn-flat btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                <span onclick="deleteItem(' . $row['id'] . ');"  title="' . trans('tax.admin.delete') . '" class="btn btn-flat btn-danger"><i class="fas fa-trash-alt"></i></span>
                ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'Component.pagination');
        $data['resultItems'] = trans('tax.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'item_total' => $dataTmp->total()]);

        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.tax')
            ->with($data);
    }


/**
 * update status
 */
    public function postEdit($id)
    {
        $tax = ShopTax::find($id);
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'value' => 'numeric|min:0',
        ],[
            'name.required' => trans('validation.required', ['attribute' => trans('tax.name')]),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
//Edit

        $dataUpdate = [
            'value' => (int)$data['value'],
            'name' => $data['name'],
        ];
        
        $tax->update($dataUpdate);

//
        return redirect()->back()->with('success', trans('tax.admin.edit_success'));

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
            ShopTax::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}

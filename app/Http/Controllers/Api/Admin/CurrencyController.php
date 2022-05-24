<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopCurrency;
use Validator;
use App\Http\Resources\CurrencyCollection;

class CurrencyController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        $searchParams = request()->all();
        $data = (new ShopCurrency)->getCurrencyListAdmin($searchParams);
        if ($data->total() == 0) {
            $data = lc_currency_default();
        }
        return CurrencyCollection::collection($data)->additional(['message' => 'Successfully']);
    }

/**
 * Form create new item in admin
 * @return [type] [description]
 */
    public function create()
    {
        $data = [
            'title' => trans('currency.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => trans('currency.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'currency' => [],
            'url_action' => bc_route_admin('admin_currency.create'),
        ];
        return view($this->templatePathAdmin.'screen.currency')
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
        $validator = Validator::make($data, [
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric|gt:0',
            'precision' => 'required',
            'symbol_first' => 'required',
            'thousands' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopCurrency::class.'",code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataInsert = [
            'name' => $data['name'],
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'exchange_rate' => $data['exchange_rate'],
            'precision' => $data['precision'],
            'symbol_first' => $data['symbol_first'],
            'thousands' => $data['thousands'],
            'status' => empty($data['status']) ? 0 : 1,
            'sort' => (int) $data['sort'],
        ];
        ShopCurrency::create($dataInsert);

        return redirect()->route('admin_currency.index')->with('success', trans('currency.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id)
    {
        $currency = ShopCurrency::find($id);
        if ($currency === null) {
            return 'no data';
        }
        $data = [
            'title' => trans('currency.admin.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'currency' => $currency,
            'url_action' => bc_route_admin('admin_currency.edit', ['id' => $currency['id']]),
        ];
        return view($this->templatePathAdmin.'screen.currency')
            ->with($data);
    }

/**
 * update status
 */
    public function postEdit($id)
    {
        $currency = ShopCurrency::find($id);
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($data, [
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric|gt:0',
            'precision' => 'required',
            'symbol_first' => 'required',
            'thousands' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopCurrency::class.'",code,' . $currency->id . ',id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
//Edit

        $dataUpdate = [
            'name' => $data['name'],
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'exchange_rate' => $data['exchange_rate'],
            'precision' => $data['precision'],
            'symbol_first' => $data['symbol_first'],
            'thousands' => $data['thousands'],
            'sort' => (int) $data['sort'],

        ];

        //Check status before change
        $check = ShopCurrency::where('status', 1)->where('code', '<>', $data['code'])->count();
        if ($check) {
            $dataUpdate['status'] = empty($data['status']) ? 0 : 1;
        } else {
            $dataUpdate['status'] = 1;
        }
        //End check status

        $obj = ShopCurrency::find($id);
        $obj->update($dataUpdate);

//
        return redirect()->route('admin_currency.index')->with('success', trans('currency.admin.edit_success'));

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
            $arrID = array_diff($arrID, BC_GUARD_CURRENCY);
            ShopCurrency::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}

<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use Validator;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = new ShopLanguage;
    }

    /**
     * Get active languages
     * @return [type] [description]
     */
    public function getActiveLanguages()
    {
        $languages = (new ShopLanguage)->getCodeActive();
        return response()->json(new JsonResponse($languages), Response::HTTP_OK);
    }
    
/**
 * Post create
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'icon' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopLanguage::class.'",code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataInsert = [
            'icon' => $data['icon'],
            'name' => $data['name'],
            'code' => $data['code'],
            'rtl' => empty($data['rtl']) ? 0 : 1,
            'status' => empty($data['status']) ? 0 : 1,
            'sort' => (int) $data['sort'],
        ];
        $obj = ShopLanguage::create($dataInsert);

        return redirect()->route('admin_language.edit', ['id' => $obj['id']])->with('success', trans('language.admin.create_success'));

    }

/**
 * Form edit
 */
public function edit($id)
{
    $language = ShopLanguage::find($id);
    if(!$language) {
        return 'No data';
    }
    $data = [
        'title' => trans('language.admin.list'),
        'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . trans('language.admin.edit'),
        'subTitle' => '',
        'icon' => 'fa fa-indent',
        'urlDeleteItem' => bc_route_admin('admin_language.delete'),
        'removeList' => 0, // 1 - Enable function delete list item
        'buttonRefresh' => 0, // 1 - Enable button refresh
        'buttonSort' => 0, // 1 - Enable button sort
        'css' => '', 
        'js' => '',
        'url_action' => bc_route_admin('admin_language.edit', ['id' => $language['id']]),
        'language' => $language,
    ];

    $listTh = [
        'id' => trans('language.id'),
        'name' => trans('language.name'),
        'code' => trans('language.code'),
        'icon' => trans('language.icon'),
        'rtl' => trans('language.layout_rtl'),
        'sort' => trans('language.sort'),
        'status' => trans('language.status'),
        'action' => trans('language.admin.action'),
    ];
    $obj = new ShopLanguage;
    $obj = $obj->orderBy('id', 'desc');
    $dataTmp = $obj->paginate(20);

    $dataTr = [];
    foreach ($dataTmp as $key => $row) {
        $dataTr[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'code' => $row['code'],
            'icon' => bc_image_render($row['icon'], '30px', '30px', $row['name']),
            'rtl' => $row['rtl'],
            'sort' => $row['sort'],
            'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
            'action' => '
                <a href="' . bc_route_admin('admin_language.edit', ['id' => $row['id']]) . '"><span title="' . trans('language.admin.edit') . '" type="button" class="btn btn-flat btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

              <span ' . (in_array($row['id'], BC_GUARD_LANGUAGE) ? "style='display:none'" : "") . ' onclick="deleteItem(' . $row['id'] . ');"  title="' . trans('language.admin.delete') . '" class="btn btn-flat btn-danger"><i class="fas fa-trash-alt"></i></span>
              ',
        ];
    }

    $data['listTh'] = $listTh;
    $data['dataTr'] = $dataTr;
    $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'Component.pagination');
    $data['resultItems'] = trans('language.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'item_total' => $dataTmp->total()]);

    $data['layout'] = 'edit';
    return view($this->templatePathAdmin.'screen.language')
        ->with($data);
}

/**
 * update
 */
    public function postEdit($id)
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
//Edit

        $dataUpdate = [
            'icon' => $data['icon'],
            'name' => $data['name'],
            'code' => $data['code'],
            'rtl' => empty($data['rtl']) ? 0 : 1,
            'sort' => $data['sort'],
        ];
        //Check status before change
        $check = ShopLanguage::where('status', 1)->where('code', '<>', $data['code'])->count();
        if ($check) {
            $dataUpdate['status'] = empty($data['status']) ? 0 : 1;
        } else {
            $dataUpdate['status'] = 1;
        }
        //End check status
        $obj = ShopLanguage::find($id);
        $obj->update($dataUpdate);

//
        return redirect()->back()->with('success', trans('language.admin.edit_success'));

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
            $arrID = array_diff($arrID, BC_GUARD_LANGUAGE);
            ShopLanguage::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}

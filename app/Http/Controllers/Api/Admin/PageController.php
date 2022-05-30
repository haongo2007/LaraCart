<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Admin\Page;
use App\Http\Resources\PageCollection;
use Validator;

class PageController extends Controller
{
    public function index()
    {
        $dataSearch = request()->all();
        $data = Page::getPageListAdmin($dataSearch);
        return PageCollection::collection($data)->additional(['message' => 'Successfully']);
    }


    /*
     * Post create new item in admin
     * @return [type] [description]
     */
    public function store()
    {

        $data = request()->all();
        $langFirst = array_key_first(bc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = bc_word_format_url($data['alias']);
        $data['alias'] = bc_word_limit($data['alias'], 100);
        $validator = Validator::make($data, [
                'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
                'descriptions.*.title' => 'required|string|max:200',
                'descriptions.*.keyword' => 'nullable|string|max:200',
                'descriptions.*.description' => 'nullable|string|max:300',
            ], [
                'alias.regex' => trans('page.alias_validate'),
                'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans('page.title')]),
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        $dataInsert = [
            'image'    => $data['image'],
            'alias'    => $data['alias'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'store_id' => session('adminStoreId'),
        ];
        $page = AdminPage::createPageAdmin($dataInsert);
        $dataDes = [];
        $languages = ShopLanguage::getListActive($data['store_id']);
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'page_id'     => $page->id,
                'lang'        => $code,
                'title'       => $data['descriptions'][$code]['title'],
                'keyword'     => $data['descriptions'][$code]['keyword'],
                'description' => $data['descriptions'][$code]['description'],
                'content'     => $data['descriptions'][$code]['content'],
            ];
        }
        AdminPage::insertDescriptionAdmin($dataDes);
        bc_clear_cache('cache_page');
        return redirect()->route('admin_page.index')->with('success', trans('page.admin.create_success'));

    }


    /*
     * update status
     */
    public function postEdit($id)
    {
        $page = AdminPage::getPageAdmin($id);
        if (!$page) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = request()->all();
        $langFirst = array_key_first(bc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = bc_word_format_url($data['alias']);
        $data['alias'] = bc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
                'descriptions.*.title' => 'required|string|max:200',
                'descriptions.*.keyword' => 'nullable|string|max:200',
                'descriptions.*.description' => 'nullable|string|max:300',
                'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
            ], [
                'alias.regex' => trans('page.alias_validate'),
                'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans('page.title')]),
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit
        $dataUpdate = [
            'image' => $data['image'],
            'status' => empty($data['status']) ? 0 : 1,
        ];
        if (!empty($data['alias'])) {
            $dataUpdate['alias'] = $data['alias'];
        }
        $page->update($dataUpdate);
        $page->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'page_id'     => $id,
                'lang'        => $code,
                'title'       => $row['title'],
                'keyword'     => $row['keyword'],
                'description' => $row['description'],
                'content'     => $row['content'],
            ];
        }
        AdminPage::insertDescriptionAdmin($dataDes);
        bc_clear_cache('cache_page');
        return redirect()->route('admin_page.index')->with('success', trans('page.admin.edit_success'));

    }

    /*
        Delete list Item
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
            AdminPage::destroy($arrID);
            bc_clear_cache('cache_page');
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminPage::getPageAdmin($id);
    }

}

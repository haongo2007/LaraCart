<?php
namespace App\Plugins\Cms\Content\Admin;

use App\Http\Controllers\RootAdminController;
use BlackCart\Core\Front\Models\ShopLanguage;
use App\Plugins\Cms\Content\Admin\Models\AdminCmsCategory;
use App\Plugins\Cms\Content\AppConfig;

use Validator;

class CmsCategoryController extends RootAdminController
{
    public $languages;
    public $plugin;

    public function __construct()
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
        $this->plugin = new AppConfig;

    }

    public function index()
    {
        $pathPlugin = $this->plugin->pathPlugin;
        $categoriesTitle =  AdminCmsCategory::getListTitleAdmin();
        
        $sort_order = bc_clean(request('sort_order') ?? 'id_desc');
        $keyword    = bc_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => trans($pathPlugin.'::Category.admin.sort_order.id_desc'),
            'id__asc' => trans($pathPlugin.'::Category.admin.sort_order.id_asc'),
            'title__desc' => trans($pathPlugin.'::Category.admin.sort_order.title_desc'),
            'title__asc' => trans($pathPlugin.'::Category.admin.sort_order.title_asc'),
        ];

        $dataSearch = [
            'keyword'    => $keyword,
            'sort_order' => $sort_order,
            'arrSort'    => $arrSort,
        ];
        $dataCmsCate = (new AdminCmsCategory)->getCategoryListAdmin($dataSearch);

        $data = [
            'title' => trans($this->plugin->pathPlugin.'::Category.admin.list'),
            'urlDeleteItem' => bc_route_admin('admin_cms_category.delete'),
            'removeList'    => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort'    => 1, // 1 - Enable button sort
            'urlSort'       => bc_route_admin('admin_cms_category.index', request()->except(['_token', '_pjax', 'sort_order'])),
            'dataCmsCate'   => $dataCmsCate,
            'resultItems'   => trans($pathPlugin.'::Category.admin.result_item', ['item_from' => $dataCmsCate->firstItem(), 'item_to' => $dataCmsCate->lastItem(), 'item_total' => $dataCmsCate->total()]),
            'pagination'    => $dataCmsCate->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination'),
            'keyword'       => $keyword,
            'arrSort'       => $arrSort,
            'sort_order'    => $sort_order,
            'pathPlugin'    => $pathPlugin,
        ];

        return view($this->plugin->pathPlugin.'::Admin.Category.list')
            ->with($data);
    }

/**
 * Form create new order in admin
 * @return [type] [description]
 */
    public function create()
    {
        $data = [
            'title' => trans($this->plugin->pathPlugin.'::Category.admin.add_new_title'),
            'title_description' => trans($this->plugin->pathPlugin.'::Category.admin.add_new_des'),
            'languages' => $this->languages,
            'category' => [],
            'categories' => (new AdminCmsCategory)->getTreeCategoriesAdmin(),
            'url_action' => bc_route_admin('admin_cms_category.create'),
        ];
        return view($this->plugin->pathPlugin.'::Admin.Category.add_edit')
            ->with($data);
    }

/**
 * Post create new order in admin
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();

        $langFirst = array_key_first(bc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = bc_word_format_url($data['alias']);
        $data['alias'] = bc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'sort' => 'numeric|min:0',
            'parent' => 'required',
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:300',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100|cms_category_alias_unique',
        ], [
            'alias.regex' => trans($this->plugin->pathPlugin.'::Category.alias_validate'),
            'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans($this->plugin->pathPlugin.'::Category.title')]),
            'alias.cms_category_alias_unique' => trans($this->plugin->pathPlugin.'::Category.alias_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        $dataInsert = [
            'image'    => $data['image'],
            'alias'    => $data['alias'],
            'parent'   => (int) $data['parent'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'sort'     => (int) $data['sort'],
            'store_id' => session('adminStoreId'),
        ];

        $category = AdminCmsCategory::createCategoryAdmin($dataInsert);
        $id = $category->id;
        $dataDes = [];
        $languages = $this->languages;
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'category_id' => $id,
                'lang'        => $code,
                'title'       => $data['descriptions'][$code]['title'],
                'keyword'     => $data['descriptions'][$code]['keyword'],
                'description' => $data['descriptions'][$code]['description'],
            ];
        }
        AdminCmsCategory::insertDescriptionAdmin($dataDes);

        bc_clear_cache('cache_cms_category');

        return redirect()->route('admin_cms_category.index')->with('success', trans($this->plugin->pathPlugin.'::Category.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id)
    {
        $category = AdminCmsCategory::getCategoryAdmin($id);

        if (!$category) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = [
            'title'             => trans($this->plugin->pathPlugin.'::Category.admin.edit'),
            'languages'         => $this->languages,
            'title_description' => trans($this->plugin->pathPlugin.'::Category.admin.edit'),
            'category'          => $category,
            'categories'        => (new AdminCmsCategory)->getTreeCategoriesAdmin(),
            'url_action'        => bc_route_admin('admin_cms_category.edit', ['id' => $category['id']]),
        ];
        return view($this->plugin->pathPlugin.'::Admin.Category.add_edit')
            ->with($data);
    }

/**
 * update status
 */
    public function postEdit($id)
    {
        $category = AdminCmsCategory::getCategoryAdmin($id);
        if (!$category) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = request()->all();

        $langFirst = array_key_first(bc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = bc_word_format_url($data['alias']);
        $data['alias'] = bc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'sort'                       => 'numeric|min:0',
            'parent'                     => 'required',
            'descriptions.*.title'       => 'required|string|max:200',
            'descriptions.*.keyword'     => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:300',
            'alias'                      => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100|cms_category_alias_unique:'.$id,
        ], [
            'alias.regex' => trans($this->plugin->pathPlugin.'::Category.alias_validate'),
            'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans($this->plugin->pathPlugin.'::Category.title')]),
            'alias.cms_category_alias_unique' => trans($this->plugin->pathPlugin.'::Category.alias_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit
        $dataUpdate = [
            'image'    => $data['image'],
            'alias'    => $data['alias'],
            'parent'   => $data['parent'],
            'sort'     => $data['sort'],
            'status'   => empty($data['status']) ? 0 : 1,
            'store_id' => session('adminStoreId'),
        ];

        $category->update($dataUpdate);
        $category->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'category_id' => $id,
                'lang'        => $code,
                'title'       => $row['title'],
                'keyword'     => $row['keyword'],
                'description' => $row['description'],
            ];
        }

        AdminCmsCategory::insertDescriptionAdmin($dataDes);

        bc_clear_cache('cache_cms_category');

        return redirect()->route('admin_cms_category.index')->with('success', trans($this->plugin->pathPlugin.'::Category.admin.edit_success'));

    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
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
            AdminCmsCategory::destroy($arrID);
            bc_clear_cache('cache_cms_category');
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminCmsCategory::getCategoryAdmin($id);
    }

}

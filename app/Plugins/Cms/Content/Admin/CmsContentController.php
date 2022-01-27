<?php
namespace App\Plugins\Cms\Content\Admin;

use App\Http\Controllers\RootAdminController;
use BlackCart\Core\Front\Models\ShopLanguage;
use App\Plugins\Cms\Content\Admin\Models\AdminCmsCategory;
use App\Plugins\Cms\Content\Admin\Models\AdminCmsContent;
use App\Plugins\Cms\Content\AppConfig;
use Validator;

class CmsContentController extends RootAdminController
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
            'id__desc'    => trans($pathPlugin.'::Content.admin.sort_order.id_desc'),
            'id__asc'     => trans($pathPlugin.'::Content.admin.sort_order.id_asc'),
            'title__desc' => trans($pathPlugin.'::Content.admin.sort_order.title_desc'),
            'title__asc'  => trans($pathPlugin.'::Content.admin.sort_order.title_asc'),
        ];

        $dataSearch = [
            'keyword'    => $keyword,
            'sort_order' => $sort_order,
            'arrSort'    => $arrSort,
        ];
        $dataCmsCont = (new AdminCmsContent)->getContentListAdmin($dataSearch);

        $data = [
            'title'         => trans($pathPlugin.'::Content.admin.list'),
            'urlDeleteItem' => bc_route_admin('admin_cms_content.delete'),
            'removeList'    => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort'    => 1, // 1 - Enable button sort,
            'keyword'       => $keyword,
            'arrSort'       => $arrSort,
            'sort_order'    => $sort_order,
            'categoriesTitle'=> $categoriesTitle,
            'pathPlugin'    => $pathPlugin,
            'dataCmsCont'   => $dataCmsCont,
            'pagination'    => $dataCmsCont->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination'),
            'resultItems'   => trans($pathPlugin.'::Content.admin.result_item', 
                                    [
                                        'item_from' => $dataCmsCont->firstItem(), 
                                        'item_to' => $dataCmsCont->lastItem(), 
                                        'item_total' => $dataCmsCont->total()
                                    ]
                                ),
            'urlSort'       => bc_route_admin('admin_cms_content.index', request()->except(['_token', '_pjax', 'sort_order'])),
        ];

        return view($this->plugin->pathPlugin.'::Admin.Content.list')
            ->with($data);
    }

    /**
     * Form create new order in admin
     * @return [type] [description]
     */
    public function create()
    {
        $data = [
            'title' => trans($this->plugin->pathPlugin.'::Content.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => trans($this->plugin->pathPlugin.'::Content.admin.add_new_des'),
            'languages' => $this->languages,
            'content' => [],
            'categories' => (new AdminCmsCategory)->getTreeCategoriesAdmin(),
            'url_action' => bc_route_admin('admin_cms_content.create'),
        ];
        return view($this->plugin->pathPlugin.'::Admin.Content.add_edit')
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
            'category_id' => 'required',
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:300',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100|cms_content_alias_unique',
        ], [
            'descriptions.*.title.required' => trans('validation.required', 
            ['attribute' => trans($this->plugin->pathPlugin.'::Content.title')]),
            'alias.regex' => trans($this->plugin->pathPlugin.'::Content.alias_validate'),
            'alias.cms_content_alias_unique' => trans($this->plugin->pathPlugin.'::Content.alias_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        $dataInsert = [
            'image'       => $data['image'],
            'alias'       => $data['alias'],
            'category_id' => (int) $data['category_id'],
            'status'      => !empty($data['status']) ? 1 : 0,
            'sort'        => (int) $data['sort'],
            'store_id'    => session('adminStoreId'),
        ];
        $content = AdminCmsContent::createContentAdmin($dataInsert);
        $id = $content->id;
        $dataDes = [];
        $languages = $this->languages;
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'content_id' => $id,
                'lang' => $code,
                'title' => $data['descriptions'][$code]['title'],
                'keyword' => $data['descriptions'][$code]['keyword'],
                'description' => $data['descriptions'][$code]['description'],
                'content' => $data['descriptions'][$code]['content'],
            ];
        }
        AdminCmsContent::insertDescriptionAdmin($dataDes);
        bc_clear_cache('cache_cms_content');
        return redirect()->route('admin_cms_content.index')
            ->with('success', trans($this->plugin->pathPlugin.'::Content.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id)
    {
        $content = AdminCmsContent::getContentAdmin($id);

        if (!$content) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = [
            'title' => trans($this->plugin->pathPlugin.'::Content.admin.edit'),
            'subTitle' => '',
            'title_description' => trans($this->plugin->pathPlugin.'::Content.admin.edit'),
            'icon' => 'fa fa-pencil-square-o',
            'languages' => $this->languages,
            'content' => $content,
            'categories' => (new AdminCmsCategory)->getTreeCategoriesAdmin(),
            'url_action' => bc_route_admin('admin_cms_content.edit', ['id' => $content['id']]),

        ];
        return view($this->plugin->pathPlugin.'::Admin.Content.add_edit')
            ->with($data);
    }

/**
 * update status
 */
    public function postEdit($id)
    {
        $content = AdminCmsContent::getContentAdmin($id);

        if (!$content) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = request()->all();
        
        $langFirst = array_key_first(bc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = bc_word_format_url($data['alias']);
        $data['alias'] = bc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'category_id' => 'required',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100|cms_content_alias_unique:'.$id,
            'sort' => 'numeric|min:0',
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:300',
        ], [
            'alias.regex' => trans($this->plugin->pathPlugin.'::Content.alias_validate'),
            'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans($this->plugin->pathPlugin.'::Content.title')]),
            'alias.cms_content_alias_unique' => trans($this->plugin->pathPlugin.'::Content.alias_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
//Edit
        $store = $data['store'] ?? [];
        $dataUpdate = [
            'image'       => $data['image'],
            'alias'       => $data['alias'],
            'category_id' => $data['category_id'],
            'sort'        => $data['sort'],
            'status'      => empty($data['status']) ? 0 : 1,
            'store_id'    => session('adminStoreId'),
        ];

        $content->update($dataUpdate);
        $content->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'content_id'  => $id,
                'lang'        => $code,
                'title'       => $row['title'],
                'keyword'     => $row['keyword'],
                'description' => $row['description'],
                'content'     => $row['content'],
            ];
        }
        AdminCmsContent::insertDescriptionAdmin($dataDes);
        bc_clear_cache('cache_cms_content');
        return redirect()->route('admin_cms_content.index')->with('success', trans($this->plugin->pathPlugin.'::Content.admin.edit_success'));

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
            AdminCmsContent::destroy($arrID);
            bc_clear_cache('cache_cms_content');
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminCmsContent::getContentAdmin($id);
    }

}

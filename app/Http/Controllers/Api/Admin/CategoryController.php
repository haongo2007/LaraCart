<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use Illuminate\Http\Request;
use Validator;
use App\Models\Admin\Category;
use App\Models\Admin\StoreCategory;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Http\Resources\CategoryCollection;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{

    public $categoryTmp = [];
    public $id_disabled;
    public $languages;

    public function __construct()
    {
        $this->languages = ShopLanguage::getListActive();
    }

    public function index()
    {        
        $searchParams = request()->all();
        $data = (new Category)->getCategoryListAdmin($searchParams);
        return CategoryCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /*
     * Post create new item in admin
     * @return [type] [description]
     */
    public function store (Request $request)
    {
        $data = $request->all();
        $data['descriptions'] = json_decode($data['descriptions']);
        $data['parent'] = json_decode($data['parent']);
        $data['parent_list'] = $data['parent'] ? implode(',',$data['parent']) : 0;
        $data['parent'] = (is_array($data['parent']) ? end($data['parent']) : $data['parent']);
        $langFirst = array_key_first(lc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions']->$langFirst->title;
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);
        if ($data['store_id'] == 0) {
            $Instance = new Category();
        }else{
            $Instance = new StoreCategory();
        }
        $validator = Validator::make($data, [
                'parent'                 => 'required',
                'sort'                   => 'numeric|min:0',
                'alias'                  => 'required|unique:"'.$Instance::class.'",alias|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
                'descriptions.*.title'   => 'required|string|max:200',
                'descriptions.*.keyword' => 'nullable|string|max:200',
                'descriptions.*.description' => 'nullable|string|max:300',
            ], [
                'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans('category.title')]),
                'alias.regex' => trans('category.alias_validate'),
            ]
        );

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        /* UPLOAD IMAGE */
        if($request->hasFile('image')){
            $path = 'category/';
            $fileName = $request->file('image')->hashName();
            $request->file('image')->storeAs(
                $path,$fileName
            );
            $data['image'] = LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX.'/getFile?disk=product&path='.$fileName;
        }
        $dataInsert = [
            'image'    => $data['image'],
            'alias'    => $data['alias'],
            'parent'   => (int) $data['parent'],
            'parent_list' => $data['parent_list'],
            'top'      => !empty($data['top']) ? 1 : 0,
            'status'   => !empty($data['status']) ? 1 : 0,
            'sort'     => (int) $data['sort'],
        ];
        if ($data['store_id'] !== 0) {
            $dataInsert['store_id'] = $data['store_id'];
        }
        $category = $Instance::createCategoryAdmin($dataInsert);
        $dataDes = [];
        $languages = $this->languages;
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'category_id' => $category->id,
                'lang'        => $code,
                'title'       => $data['descriptions']->$code->title,
                'keyword'     => implode(',',$data['descriptions']->$code->keyword) ,
                'description' => $data['descriptions']->$code->description,
            ];
        }
        $Instance::insertDescriptionAdmin($dataDes);

        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
     * API show
     */
    public function show($id)
    {
        if (count(session('adminStoreId')) == 1) {
            $Instance = new StoreCategory();
        }else{
            $Instance = new Category();
        }
        $category = $Instance::with('descriptions')->find($id);
        if (!$category) {
            return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new JsonResponse($category), Response::HTTP_OK);
    }

    /*
     * update status
     */
    public function update(Request $request,$id)
    {
        $data = request()->all();
        if ($data['store_id'] == 0) {
            $Instance = new Category();
        }else{
            $Instance = new StoreCategory();
        }

        $category = $Instance::getCategoryAdmin($id);
        if (!$category) {
            return response()->json(new JsonResponse([], 'Data not Found'), Response::HTTP_FORBIDDEN);
        }
        $data['descriptions'] = json_decode($data['descriptions']);
        $data['parent'] = json_decode($data['parent']);
        $data['parent_list'] = (is_array($data['parent']) ? implode(',',$data['parent']) : $data['parent']);
        $data['parent'] = (is_array($data['parent']) ? end($data['parent']) : $data['parent']);


        $langFirst = array_key_first(lc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'parent'                 => 'required',
            'sort'                   => 'numeric|min:0',
            'alias'                  => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100|unique:"'.$Instance::class.'",alias,' . $id . '',
            'descriptions.*.title'   => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:300',
            ], [
                'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans('category.title')]),
                'alias.regex'                   => trans('category.alias_validate'),
            ]
        );

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        /* UPLOAD IMAGE */
        if($request->hasFile('image')){
            $path = 'category/';
            $fileName = $request->file('image')->hashName();
            $request->file('image')->storeAs(
                $path,$fileName
            );
            $data['image'] = 'api/getFile?disk=category&path='.$fileName;
        }

        //Edit
        $dataUpdate = [
            'image'    => $data['image'],
            'alias'    => $data['alias'],
            'parent'   => $data['parent'],
            'sort'     => $data['sort'],
            'top'      => empty($data['top']) ? 0 : 1,
            'status'   => empty($data['status']) ? 0 : 1,
        ];

        $category->update($dataUpdate);
        $category->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'category_id' => $id,
                'lang'        => $code,
                'title'       => $row->title,
                'keyword'     => implode(',', $row->keyword),
                'description' => $row->description,
            ];
        }
        $Instance::insertDescriptionAdmin($dataDes);

        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(new JsonResponse([],trans('admin.method_not_allow')), Response::HTTP_OK);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrID = array_filter($arrID);
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if(!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(new JsonResponse([],trans('admin.remove_dont_permisison'). ': ' . json_encode($arrDontPermission)), Response::HTTP_FORBIDDEN);
            }
            Category::destroy($arrID);

            return response()->json(new JsonResponse([]), Response::HTTP_OK);
        }
    }
    /*
    Delete Item
    Need mothod destroy to boot deleting in model
    */
    public function destroy(Category $category)
    {
        if ($category->Children()->count() > 0) {
            return response()->json(new JsonResponse([],'This record is currently linked to another that cannot be deleted'), Response::HTTP_FORBIDDEN);
        }else{
            try {
                $category->delete();
                return response()->json(new JsonResponse([]), Response::HTTP_OK);
            } catch (\Exception $ex) {
                return response()->json(new JsonResponse([],$ex->getMessage()), Response::HTTP_FORBIDDEN);
            }
        }
    }
    
    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return Category::getCategoryAdmin($id);
    }

    /**
     * Get Recursive category.
     *
     * @param  null
     * @return \Illuminate\Http\Response
     */
    public function Recursive(Request $request){
        if ($request->id) {
            $this->id_disabled = $request->id;
        }
        $data = $this->ExcuteRecursive();
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }
    public function ExcuteRecursive($parent = 0)
    {
        $data = [];
        foreach ($this->getCategory($parent) as $group)
        {
            $group['children'] = $this->ExcuteRecursive($group['id']);
            if (empty($group['children'])) {
                unset($group['children']);
            }
            $data[] = $group;
        }
        return $data;
    }
    public function getCategory($parent)
    {
        $tmp = [];
        $category = $this->categoryTmp;
        if (empty($category)) {
            $category = Category::with('descriptionsWithLangDefault:title,category_id')->select('id','parent')->get();
            $this->categoryTmp = $category;
        }
        foreach ($category as $key => $value) {
            if ($value->descriptionsWithLangDefault != null) {
                $value['title'] = $value->descriptionsWithLangDefault->title;
            }
            unset($value['descriptionsWithLangDefault']);
            if ($value['parent'] == $parent) {
                if ($this->id_disabled && $parent != 0) {
                    if ($this->id_disabled == $value['id'] || $this->id_disabled == $value['parent']) {
                        $value['disabled'] = true;
                    }
                }
                $tmp[] = $value->toArray();
            }
        }
        return $tmp;
    }
    /*
     * API get childrend
     */
    public function getChildren(Request $request)
    {
        $category = Category::where('parent',$request->id)->get();
        if (!$category) {
            return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
        }
        return response()->json(new JsonResponse(CategoryCollection::collection($category)), Response::HTTP_OK);
    }
    /*
     * API get nested show
     */
    public function getNested(Request $request)
    {
        dd($request->ids);
        $category = Category::where('parent',$request->id)->get();
        if (!$category) {
            return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
        }
        return response()->json(new JsonResponse(CategoryCollection::collection($category)), Response::HTTP_OK);
    }
}

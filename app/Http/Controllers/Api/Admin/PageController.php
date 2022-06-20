<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Admin\Page;
use App\Models\Front\ShopPageDescription;
use App\Http\Resources\PageCollection;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
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
        $langFirst = array_key_first(lc_language_all($data['store_id'])->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['title'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);
        $validator = Validator::make($data, [
                'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
                'title' => 'required|string|max:200',
                'keyword' => 'nullable|array',
                'description' => 'nullable|string|max:300',
            ], [
                'alias.regex' => trans('page.alias_validate'),
                'title.required' => trans('validation.required', ['attribute' => trans('page.title')]),
            ]
        );

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        if ($data['page_id'] == 0) {
            $dataInsert = [
                'image'    => is_array($data['image']) ? $data['image'][0] : $data['image'],
                'alias'    => $data['alias'],
                'status'   => !empty($data['status']) ? 1 : 0,
                'store_id' => $data['store_id'],
            ];
            $page = Page::createPageAdmin($dataInsert);
            $page_id = $page->id;
        }else{
            $page_id = $data['page_id'];
        }
        $dataDes[] = [
            'page_id'     => $page_id,
            'lang'        => $data['lang'],
            'title'       => $data['title'],
            'keyword'     => $data['keyword'] ? implode(',', $data['keyword']) : null,
            'description' => $data['description'],
            'content'     => $data['content'],
            'design'     => json_encode($data['design']),
        ];
        Page::insertDescriptionAdmin($dataDes);
        lc_clear_cache('cache_page');

        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
     * API show
     */
    public function show($id,$lang)
    {
        $page = Page::with(['descriptions' => function ($query) use ($lang)
        {
            $query->where('lang',$lang);
        }])->find($id);
        
        if (!$page) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new JsonResponse($page), Response::HTTP_OK);
    }

    /*
     * update status
     */
    public function update($id)
    {
        $page = Page::getPageAdmin($id);
        if (!$page) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }

        $data = request()->all();
        $langFirst = array_key_first(lc_language_all($data['store_id'])->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['title'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
                'title' => 'required|string|max:200',
                'keyword' => 'nullable|array|max:200',
                'description' => 'nullable|string|max:300',
                'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
            ], [
                'alias.regex' => trans('page.alias_validate'),
                'title.required' => trans('validation.required', ['attribute' => trans('page.title')]),
            ]
        );

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        //Edit
        $dataUpdate = [
            'image'    => is_array($data['image']) ? $data['image'][0] : $data['image'],
            'status' => empty($data['status']) ? 0 : 1,
        ];
        if (!empty($data['alias'])) {
            $dataUpdate['alias'] = $data['alias'];
        }
        $page->update($dataUpdate);
        $page->descriptions()->delete();
        $dataDes[] = [
            'page_id'     => $id,
            'lang'        => $data['lang'],
            'title'       => $data['title'],
            'keyword'     => $data['keyword'] ? implode(',', $data['keyword']) : null,
            'description' => $data['description'],
            'content'     => $data['content'],
            'design'     => json_encode($data['design']),
        ];
        Page::insertDescriptionAdmin($dataDes);
        lc_clear_cache('cache_page');

        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
        Delete list Item
        Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $ids = $id;
        $arrID = explode(',', $ids);
        foreach ($arrID as $key => $id) {
            $val = explode('.', $id);
            $check = ShopPageDescription::where('page_id',$val[0])->count();
            if ($check < 2) {
                Page::find($val[0])->delete();
            }else{
                ShopPageDescription::where([['page_id',$val[0]],['lang',$val[1]]])->delete();
            }
        }
        lc_clear_cache('cache_page');
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }
}

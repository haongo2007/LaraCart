<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Admin\News;
use App\Http\Resources\BlogsCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Validator;

class BlogsController extends Controller
{
    public function index()
    {
        $dataSearch = request()->all();
        $data = News::getNewsListAdmin($dataSearch);
        return BlogsCollection::collection($data)->additional(['message' => 'Successfully']);
    }

/**
 * Post create new item in admin
 * @return [type] [description]
 */
    public function store()
    {
        $data = request()->all();
        $langFirst = array_key_first(lc_language_all($data['store_id'])->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|array|max:200',
            'descriptions.*.description' => 'nullable|string|max:300',
            ], [
                'alias.regex' => trans('news.alias_validate'),
                'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans('news.title')]),
            ]
        );
        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'image'    => is_array($data['image']) ? implode(',',$data['image']) : $data['image'],
            'sort'     => $data['sort'],
            'alias'    => $data['alias'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'store_id' => $data['store_id'],
        ];
        $news = News::createNewsAdmin($dataInsert);
        $id = $news->id;
        $dataDes = [];
        $languages = (new ShopLanguage)->getCodeActive($data['store_id']);
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'news_id'     => $id,
                'lang'        => $code,
                'title'       => $data['descriptions'][$code]['title'],
                'keyword'     => implode(',',$data['descriptions'][$code]['keyword']),
                'description' => $data['descriptions'][$code]['description'],
                'content'     => $data['descriptions'][$code]['content'],
            ];
        }
        News::insertDescriptionAdmin($dataDes);
        lc_clear_cache('cache_news');
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }
    /*
     * show News
     */
    public function show($id)
    {
        $news = News::with('descriptions')->find($id);
        if (!$news) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }

        return response()->json(new JsonResponse($news), Response::HTTP_OK);
    }

    /**
     * update status
     */
    public function update($id)
    {
        $news = News::getNewsAdmin($id);
        if (!$news) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_FORBIDDEN);
        }
        $data = request()->all();

        $langFirst = array_key_first(lc_language_all($data['store_id'])->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|array|max:200',
            'descriptions.*.description' => 'nullable|string|max:300',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
            ], [
                'alias.regex' => trans('news.alias_validate'),
                'descriptions.*.title.required' => trans('validation.required', ['attribute' => trans('news.title')]),
            ]
        );

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
    //Edit
        $dataUpdate = [
            'image' => is_array($data['image']) ? implode(',',$data['image']) : $data['image'],
            'alias' => $data['alias'],
            'sort' => $data['sort'],
            'status' => !empty($data['status']) ? 1 : 0,
            'store_id'  => $data['store_id'],
        ];

        $news->update($dataUpdate);
        $news->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'news_id' => $id,
                'lang' => $code,
                'title' => $row['title'],
                'keyword' => implode(',',$row['keyword']),
                'description' => $row['description'],
                'content' => $row['content'],
            ];
        }
        News::insertDescriptionAdmin($dataDes);
        lc_clear_cache('cache_news');
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        News::destroy($arrID);
        lc_clear_cache('cache_news');

        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}

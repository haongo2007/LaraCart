<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopNews;
use Illuminate\Http\Request;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\Front\BlogsCollection;

class ShopNewsController extends Controller
{
    /**
     * Render news
     * @return [type] [description]
     */
    private function index()
    {
        $data = (new ShopNews)
            ->setLimit(lc_config('news_list'))
            ->setPaginate()
            ->getData();
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }

    /**
     * Get blog detail
     *
     * @param   [string]  $alias      [$alias description]
     *
     * @return  [mix]
     */
    public function show(Request $request,$alias) 
    {
        $blog = (new ShopNews)->getDetail($alias, $type ='alias');
        
        $data['blog'] = new BlogsCollection($blog);
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }

}

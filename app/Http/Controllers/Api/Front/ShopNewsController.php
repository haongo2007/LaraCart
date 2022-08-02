<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopNews;
use Illuminate\Http\Request;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;

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
     * News detail
     *
     * @param   [string]  $alias 
     *
     * @return  view
     */
    private function show($alias)
    {
        $data = (new ShopNews)->getDetail($alias, $type ='alias');
        if (!$data) {
            return response()->json(new JsonResponse([],'Content not found'), Response::HTTP_NOT_FOUND);
        } else {
            return response()->json(new JsonResponse($data), Response::HTTP_OK);
        }
    }

}

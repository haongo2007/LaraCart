<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

class CacheConfigController extends Controller
{
    /**
     * Clear cache
     *
     * @return  json
     */
    public function clearCache() {
        $action = request('action');
        $storeId = request('store_id');
        $response = lc_clear_cache($action,$storeId);
        return response()->json(new JsonResponse($response), Response::HTTP_OK);
    }
}

<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin\Log;
use App\Models\Admin\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogsCollection;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;

class LogController extends Controller
{
    
    public function index()
    {
        $searchParams = request()->all();
        $data = (new Log)->getLogsListAdmin($searchParams);
        return LogsCollection::collection($data)->additional(['message' => 'Successfully']);
    }

/*
Delete list item
Need mothod destroy to boot deleting in model
 */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        Log::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}

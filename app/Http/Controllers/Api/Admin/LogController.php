<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin\Log;
use App\Models\Admin\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogsCollection;

class LogController extends Controller
{

    public function __construct()
    {
    }
    
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
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => trans('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            AdminLog::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}

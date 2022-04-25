<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alexusmai\LaravelFileManager\Events\BeforeInitialization;
use Alexusmai\LaravelFileManager\FileManager;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

/**
 * Class LaracartController
 *
 * @package App\Http\Controllers
 */
class LaraCartController extends Controller
{
     /**
     * @var FileManager
     */

    public $fm;
    /**
     * FileManagerController constructor.
     *
     * @param FileManager $fm
     */
    public function __construct(FileManager $fm)
    {
        $this->fm = $fm;
    }

    /**
     * Entry point for Laracart Dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('laraCart');
    }
    /**
     * Get file URL
     *
     * @param $disk
     * @param $path
     *
     * @return array
     */
    public function url(Request $request)
    {
        if (is_array($request->path)) {
            foreach ($request->path as $key => $uri) {
                $uri = json_decode($uri);
                $res[] = 'api/'.config('const.LC_ADMIN_PREFIX').'/getFile?disk='.$request->disk.'&path='.urlencode($uri->path);
            }
        }else{
            $res[] = 'api/'.config('const.LC_ADMIN_PREFIX').'/getFile?disk='.$request->disk.'&path='.urlencode($request->path);  
        }
        return response()->json(new JsonResponse($res), Response::HTTP_OK);
    }
    public function initialize()
    {
        event(new BeforeInitialization());
        $init = $this->fm->initialize();
        $disk_size = disk_total_space(substr(public_path(), 0, 2)) / pow(1024, 3);
        $free_space = disk_free_space(substr(public_path(), 0, 2))  / pow(1024, 3);

        $init['config']['disks'] = [request()->disk => ["driver" => "local" , "capacity" => (int) $disk_size , "free_space" => (int) $free_space] ];
        return response()->json(
            $init
        );
    }
}

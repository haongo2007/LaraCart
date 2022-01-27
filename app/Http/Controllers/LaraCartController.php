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
        $init['config']['disks'] = [request()->disk => ["driver" => "local"] ];
        return response()->json(
            $init
        );
    }
}

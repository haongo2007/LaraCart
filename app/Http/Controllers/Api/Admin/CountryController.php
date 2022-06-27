<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopCountry;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Intervention\Image\Facades\Image;

class CountryController extends Controller
{
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $data = ShopCountry::getCodeAll();
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }
    /**
     * show current country
     *
     * @return Content
     */
    public function flags($code)
    {
        return asset('svg/flags/'.$code.'.svg');
    }
}

<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopCountry;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

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
}

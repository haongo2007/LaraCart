<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopProductProperty;
use App\Models\Front\ShopLanguage;
use App\Models\Admin\Product;
use App\Http\Resources\ReportProductCollection;

class ReportController extends Controller
{
    public function __construct()
    {

    }

    public function product()
    {
        $dataSearch = request()->all();
        $data = (new Product)->getProductListAdmin($dataSearch);
        return ReportProductCollection::collection($data)->additional(['message' => 'Successfully']);
    }

}

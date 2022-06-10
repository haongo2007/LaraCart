<?php
namespace App\Http\Controllers\Api\Front;

use App\Models\Front\ShopDiscount;
use App\Models\Front\ShopOrderTotal;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Helper\JsonResponse;

class ShopDiscountController extends Controller
{
    /**
     * [checkCoupon description]
     * @return [type]           [description]
     */
    public function checkCoupon(Request $request)
    {
        $code = $request->code;
        $uID = $request->uID;
        $total =  $request->total;
        $storeId = request()->header('x-store');
        $check = (new ShopDiscount)->check($code, $uID,$storeId);
        $message = '';
        $err = true;
        if (!$check) {
            $status = Response::HTTP_NOT_FOUND;
            $message = trans('promotion.process.invalid');
        } else {
            $status = Response::HTTP_OK;
            $message = trans('promotion.process.completed');
            $value = (new ShopDiscount)->getValue($total,$check);
            $check['value'] = $value;
        }

        return response()->json(new JsonResponse($check,$message), $status);

    }

}

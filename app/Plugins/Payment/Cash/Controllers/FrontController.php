<?php
#App\Plugins\Payment\Cash\Controllers\FrontController.php
namespace App\Plugins\Payment\Cash\Controllers;

use App\Http\Controllers\Api\Front\ShopCheckoutController;
use App\Http\Controllers\Controller;
class FrontController extends Controller
{
    /**
     * Process order
     *
     * @return  [type]  [return description]
     */
    public function processOrder($orderID,$shippingMethod,$paymentMethod){
        
        return (new ShopCheckoutController)->completeOrder($orderID,$shippingMethod,$paymentMethod);
    }
}

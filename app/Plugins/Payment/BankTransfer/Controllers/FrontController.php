<?php
#App\Plugins\Payment\BankTransfer\Controllers\FrontController.php
namespace App\Plugins\Payment\BankTransfer\Controllers;

use BlackCart\Core\Front\Controllers\ShopCartController;
use App\Http\Controllers\RootFrontController;
class FrontController extends RootFrontController
{
    /**
     * Process order
     *
     * @return  [type]  [return description]
     */
    public function processOrder($orderID,$shippingMethod,$paymentMethod){
        
        return (new ShopCartController)->completeOrder($orderID,$shippingMethod,$paymentMethod);
    }
}

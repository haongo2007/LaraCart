<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\News;
use App\Models\Admin\Product;
use App\Models\Admin\Customer;
use App\Models\Admin\Order;
use App\Models\Admin\Store;
use App\Models\Front\ShopStoreDescription;
use Illuminate\Http\Request;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{  
    public function index(Request $request)
    {
        $req = $request->all();
        if (empty($req)) {
            $from = Carbon::now()->subWeek()->format('Y-m-d H:i:s');
            $to = Carbon::now()->format('Y-m-d H:i:s');
        }else{  
            $from = Carbon::parse($req['range_picker'][0])->format('Y-m-d H:i:s');
            $to = Carbon::parse($req['range_picker'][1])->format('Y-m-d H:i:s');   
        }
        if (session('adminStoreId')) {
            $storeId = session('adminStoreId');
        }else{
            $storeId = array_keys(Store::getStoreActive());
        }
        $newVisitis = Customer::getSumCustomerTotalCustomTime($from,$to,$storeId)->toArray();
        $newOrder = Order::getSumOrderTotalCustomTime($from,$to,$storeId)->toArray();
        $newProduct = Product::getSumProductTotalCustomTime($from,$to,$storeId)->toArray();
        $Companies = ShopStoreDescription::select('store_id','title')->where('lang',lc_get_locale())->whereIn('store_id',$storeId)->get()
        ->pluck('store_id','title')->toArray();
        $data = [];
        // $rangDays = CarbonPeriod::create($from, $to);

        $Header = ["value","Company","Date"];
        $Customer  = [];
        $Orders  = [];
        $Amount  = [];
        $Product  = [];
        $Customer_total = [];
        $Orders_total = [];
        $Amount_total = [];
        $Product_total = [];
        // $from = $rangDays->getStartDate()->format('Y-m-d');
        // $to = $rangDays->getEndDate()->format('Y-m-d');

        foreach($newVisitis as $k => $visited){
            $Customer[] = [$visited['total_customer'],$visited['store_id'],$visited['d']];
            $Customer_total[] = $visited['total_customer'];
        }

        foreach($newOrder as $k => $order){
            $Orders[] = [$order['total_order'],$order['store_id'],$order['d']];
            $Orders_total[] = $order['total_order'];
            
            $Amount[] = [$order['total_amount'],$order['store_id'],$order['d']];
            $Amount_total[] = $order['total_amount'];
        }

        foreach($newProduct as $k => $product){
            $Product[] = [$product['total_product'],$product['store_id'],$product['d']];
            $Product_total[] = $product['total_product'];
        }
        $Customer = array_merge([$Header],$Customer);
        $Orders = array_merge([$Header],$Orders);
        $Product = array_merge([$Header],$Product);
        $Amount = array_merge([$Header],$Amount);

        $data['newCustomers']['data'] = $Customer;
        $data['newCustomers']['total'] = array_sum($Customer_total);
        $data['newCustomers']['name'] = trans('admin.newCustomer'); 

        $data['newOrder']['data'] = $Orders;
        $data['newOrder']['total'] = array_sum($Orders_total);
        $data['newOrder']['name'] = trans('admin.newOrder');

        $data['newRevenue']['data'] = $Amount;
        $data['newRevenue']['total'] = array_sum($Amount_total);
        $data['newRevenue']['name'] = trans('admin.revenue');

        $data['newProduct']['data'] = $Product;
        $data['newProduct']['total'] = array_sum($Product_total);
        $data['newProduct']['name'] = trans('admin.newProduct');

        $data['rangeDate'] = ['from' => $from, 'to' => $to];
        $data['companies'] = $Companies;

        return response()->json(new JsonResponse($data),Response::HTTP_OK);
    }

    /**
     * Order overview
     *
     * @return  [type]  [return description]
     */
    public function orders()
    {
        $rowsNumber = 8;
        $data = Order::getListOrderNew($rowsNumber,session('adminStoreId'));
        return response()->json(new JsonResponse(['items' => $data]));
    }

    /**
     * Page deny
     *
     * @return  [type]  [return description]
     */
    public function deny()
    {
        $data = [
            'title' => trans('admin.deny'),
            'icon' => '',
            'method' => session('method'),
            'url' => session('url'),
        ];
        return view($this->templatePathAdmin.'Layout.deny', $data);
    }

    /**
     * [denySingle description]
     *
     * @return  [type]  [return description]
     */
    public function denySingle()
    {
        $data = [
            'method' => session('method'),
            'url' => session('url'),
        ];
        return view($this->templatePathAdmin.'Layout.deny_single', $data);
    }
}

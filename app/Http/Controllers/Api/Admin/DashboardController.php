<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\News;
use App\Models\Admin\Product;
use App\Models\Admin\Customer;
use App\Models\Admin\Order;
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
        
        $newVisitis = Customer::getSumCustomerTotalCustomTime($from,$to)->keyBy('d')->toArray();
        $newOrder = Order::getSumOrderTotalCustomTime($from,$to)->keyBy('d')->toArray();
        $newProduct = Product::getSumProductTotalCustomTime($from,$to)->keyBy('d')->toArray();
        
        $data = [];

        $rangDays = CarbonPeriod::create($from, $to);

        $Customer  = [];
        $Orders  = [];
        $Amount  = [];
        $Product  = [];
        $label = [];
        $from = $rangDays->getStartDate()->format('Y-m-d');
        $to = $rangDays->getEndDate()->format('Y-m-d');
        foreach ($rangDays as $i => $day) {
            $date = $day->format('m-d');
            // customer
            $Customer[] = $newVisitis[$date]['total_customer'] ?? 0;
            // order 
            $Orders[] = ($newOrder[$date]['total_order'] ?? 0);
            // revenue
            $Amount[] = ($newOrder[$date]['total_amount'] ?? 0);
            // product
            $Product[] = ($newProduct[$date]['total_product'] ?? 0);
            // label chart
            $label[] = $day->format('d-m');
        }
        $data['newCustomers']['data'] = $Customer;
        $data['newCustomers']['total'] = array_sum($Customer);
        $data['newCustomers']['name'] = 'Customers '; 

        $data['newOrder']['data'] = $Orders;
        $data['newOrder']['total'] = array_sum($Orders);
        $data['newOrder']['name'] = 'Order ';

        $data['newRevenue']['data'] = $Amount;
        $data['newRevenue']['total'] = array_sum($Amount);
        $data['newRevenue']['name'] = 'Revenue ';

        $data['newProduct']['data'] = $Product;
        $data['newProduct']['total'] = array_sum($Product);
        $data['newProduct']['name'] = 'Product ';

        $data['rangeDate'] = ['from' => $from, 'to' => $to];
        $data['label'] = $label;

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
        $data = Order::getListOrderNew($rowsNumber);
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

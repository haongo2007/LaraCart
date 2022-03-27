<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Front\ShopCurrency;
use App\Models\Admin\Config;
use App\Models\Admin\Store;
use App\Models\Front\ShopTax;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\StoreCollection;

class StoreController extends Controller
{
    public $currencies, $languages, $timezones;

    public function __construct()
    {
        foreach (timezone_identifiers_list() as $key => $value) {
            $timezones[$value] = $value;
        }
        $this->currencies = ShopCurrency::getCodeActive();
        $this->languages = ShopLanguage::getListActive();
        $this->timezones = $timezones;

    }

    public function index() {
        $searchParams = request()->all();
        $data = (new Store)->getStoreListAdmin($searchParams);
        return StoreCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    public function show($id) {
        $store = (new Store)->with('descriptions')->find($id);
        if (!$store) {
            return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
        }
        
        $store['timezones'] = $this->timezones;
        $store['languages'] = $this->languages;
        $store['currencies'] =$this->currencies;
        return response()->json(new JsonResponse($store), Response::HTTP_OK);
    }
    
    public function getConfig($id)
    {
        //Customer config
        $dataCustomerConfig = [
            'code' => 'customer_config_attribute',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $customerConfigs = Config::getListConfigByCode($dataCustomerConfig);
        $dataCustomerConfigRequired = [
            'code' => 'customer_config_attribute_required',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $customerConfigsRequired = Config::getListConfigByCode($dataCustomerConfigRequired);
        //End customer

        //Product config
        $taxs = ShopTax::pluck('name', 'id')->toArray();
        $taxs[0] = trans('tax.admin.non_tax');

        $productConfigQuery = [
            'code' => 'product_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $productConfig = Config::getListConfigByCode($productConfigQuery);

        $productConfigAttributeQuery = [
            'code' => 'product_config_attribute',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $productConfigAttribute = Config::getListConfigByCode($productConfigAttributeQuery);

        $productConfigAttributeRequiredQuery = [
            'code' => 'product_config_attribute_required',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $productConfigAttributeRequired = Config::getListConfigByCode($productConfigAttributeRequiredQuery);

        $orderConfigQuery = [
            'code' => 'order_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $orderConfig = Config::getListConfigByCode($orderConfigQuery);

        $configDisplayQuery = [
            'code' => 'display_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $configDisplay = Config::getListConfigByCode($configDisplayQuery);

        $configCaptchaQuery = [
            'code' => 'captcha_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $configCaptcha = Config::getListConfigByCode($configCaptchaQuery);

        $configCustomizeQuery = [
            'code' => 'admin_custom_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $configCustomize = Config::getListConfigByCode($configCustomizeQuery);

        

        $emailConfigQuery = [
            'code' => ['smtp_config', 'email_action'],
            'storeId' => $id,
            'groupBy' => 'code',
            'sort'    => 'asc',
        ];
        $emailConfig = Config::getListConfigByCode($emailConfigQuery);

        $data['emailConfig'] = $emailConfig;
        $data['smtp_method'] = ['' => 'None Secirity', 'TLS' => 'TLS', 'SSL' => 'SSL'];
        $data['captcha_page'] = [
            'register' => trans('captcha.captcha_page_register'), 
            'forgot'   => trans('captcha.captcha_page_forgot_password'), 
            'checkout' => trans('captcha.captcha_page_checkout'), 
            'contact'  => trans('captcha.captcha_page_contact'),
            'review'   => trans('captcha.captcha_page_review'),
        ];
        //End email
        $data['customerConfigs']                = $customerConfigs;
        $data['customerConfigsRequired']        = $customerConfigsRequired;
        $data['productConfig']                  = $productConfig;
        $data['productConfigAttribute']         = $productConfigAttribute;
        $data['productConfigAttributeRequired'] = $productConfigAttributeRequired;
        $data['pluginCaptchaInstalled']         = lc_get_plugin_captcha_installed();
        $data['taxs']                           = $taxs;
        $data['configDisplay']                  = $configDisplay;
        $data['orderConfig']                    = $orderConfig;
        $data['configCaptcha']                  = $configCaptcha;
        $data['configCustomize']                = $configCustomize;
        $data['timezones']                      = $this->timezones;
        $data['languages']                      = $this->languages;
        $data['currencies']                     = $this->currencies;
        $data['storeId']                        = $id;
        $data['urlUpdateConfig']                = lc_route_admin('admin_config.update');
        $data['urlUpdateConfigGlobal']          = lc_route_admin('admin_config_global.update');

        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }
}

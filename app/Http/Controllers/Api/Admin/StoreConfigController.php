<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Front\ShopCurrency;
use App\Models\Admin\Config;
use App\Models\Front\ShopTax;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class StoreConfigController extends Controller
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

    public function show($id)
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
        $customerConfigsRequired = Config::getListConfigByCode($dataCustomerConfigRequired)->toArray();

        foreach ($customerConfigs as $key => $value) {
            $value->required = ['value' => $customerConfigsRequired[$key.'_required']['value'],'id' => $customerConfigsRequired[$key.'_required']['id']];
        }
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

        foreach ($productConfigAttribute as $key => $value) {
            if ($productConfigAttributeRequired->has($key.'_required')) {
            $value->required = ['value' => $productConfigAttributeRequired[$key.'_required']['value'],'id' => $productConfigAttributeRequired[$key.'_required']['id']];
            }
        }
        //End Product config

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
        $displayConfig = Config::getListConfigByCode($configDisplayQuery);

        $configCaptchaQuery = [
            'code' => 'captcha_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $captchaConfig = Config::getListConfigByCode($configCaptchaQuery);

        $configCustomizeQuery = [
            'code' => 'admin_custom_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $customizeConfig = Config::getListConfigByCode($configCustomizeQuery);

        

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

        $configAdmin = [
            'code' => 'admin_config',
            'storeId' => $id,
            'keyBy' => 'key',
        ];
        $adminConfig = Config::getListConfigByCode($configAdmin);
        //End email
        $data['customerConfigs']                = $customerConfigs;
        $data['productConfig']                  = $productConfig;
        $data['productConfigAttribute']         = $productConfigAttribute;
        $data['pluginCaptchaInstalled']         = lc_get_plugin_captcha_installed();
        $data['taxs']                           = $taxs;
        $data['displayConfig']                  = $displayConfig;
        $data['orderConfig']                    = $orderConfig;
        $data['captchaConfig']                  = $captchaConfig;
        $data['customizeConfig']                = $customizeConfig;
        $data['timezones']                      = $this->timezones;
        $data['languages']                      = $this->languages;
        $data['currencies']                     = $this->currencies;
        $data['adminConfig']                    = $adminConfig;
        $data['storeId']                        = $id;
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }
    /*
    Update value config store
    */
    public function update(Request $request,$id)
    {
        $data = $request->all();

        if (!$id) {
            return response()->json(new JsonResponse([],'Store not found'), Response::HTTP_NOT_FOUND);
        }
        $config = Config::find($data['id']);
        if (!$config) {
            return response()->json(new JsonResponse([],'Store not found'), Response::HTTP_NOT_FOUND);
        }
        if (array_key_exists('required', $data)) {
            $configRequired = Config::find($data['required']['id']);
            $configRequired->value = $data['required']['value'];
            $configRequired->save();
        }
        $config->value = $data['value'];
        $config->save();
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }

}

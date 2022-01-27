<?php
#App\Plugins\Other\ProductSale\Controllers\FrontController.php
namespace App\Plugins\Other\ProductSale\Controllers;

use App\Plugins\Other\ProductSale\AppConfig;
use App\Http\Controllers\RootFrontController;
class FrontController extends RootFrontController
{
    public $plugin;

    public function __construct()
    {
        parent::__construct();
        $this->plugin = new AppConfig;
    }

    public function index() {
        return view($this->plugin->pathPlugin.'::Front',
            [
                //
            ]
        );
    }

    /**
     * Process front flash sale
     *
     * @param [type] ...$params
     * @return void
     */
    public function flashSaleProcessFront(...$params) 
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            bc_lang_switch($lang);
        }
        return $this->_flashSaleProcess();
    }


    /**
     * flashSaleProcess product
     * @return [view]
     */
    private function _flashSaleProcess()
    {
        $filter_sort = request('filter_sort') ?? '';
        if (function_exists('bc_product_flash')) {
            $products = bc_product_flash(bc_config('product_list'), $paginate = true);
        } else {
            $products = [];
        }
        bc_check_view($this->templatePath . '.Shop.product_list');
        return view(
            $this->templatePath . '.Shop.product_list',
            array(
                'title' => bc_language_render('front.flash_title'),
                'products' => $products,
                'layout_page' => 'shop_product_list',
                'filter_sort' => $filter_sort,
            )
        );
    }
}

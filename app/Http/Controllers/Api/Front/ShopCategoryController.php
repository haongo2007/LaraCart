<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopCategory;
use App\Models\Front\ShopProduct;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

class ShopCategoryController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Process front get category all
     *
     * @param [type] ...$params
     * @return void
     */
    public function getListChild($id_parent) {
        // if (config('app.seoLang')) {
        //     $lang = $params[0] ?? '';
        //     lc_lang_switch($lang);
        // }
        $store = request()->header('x-store');
        if (!$store) {
            return response()->json(new JsonResponse([],'Error'), Response::HTTP_FORBIDDEN);
        }
        $categoriesList = (new ShopCategory)->with('descriptionsWithLangDefault')->where([['parent',$id_parent],['store_id',$store]])->get();

        return response()->json(new JsonResponse($categoriesList), Response::HTTP_OK);
    }

    /**
     * display list category root (parent = 0)
     * @return [view]  
     */
    private function _allCategories()
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $filter_sort = request('filter_sort') ?? '';
        $filterArr = [
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        if (array_key_exists($filter_sort, $filterArr)) {
            $sortBy = $filterArr[$filter_sort][0];
            $sortOrder = $filterArr[$filter_sort][1];
        }

        $itemsList = (new ShopCategory)
            ->getCategoryRoot()
            ->setSort([$sortBy, $sortOrder])
            ->setPaginate()
            ->setLimit(lc_config('item_list'))
            ->getData();

        lc_check_view($this->templatePath . '.screen.shop_item_list');
        return view(
            $this->templatePath . '.screen.shop_item_list',
            array(
                'title' => trans('front.categories'),
                'itemsList' => $itemsList,
                'keyword' => '',
                'description' => '',
                'layout_page' => 'shop_item_list',
                'filter_sort' => $filter_sort,
            )
        );
    }

    /**
     * Process front get category detail
     *
     * @param [type] ...$params
     * @return void
     */
    public function categoryDetailProcessFront(...$params) {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            $alias = $params[1] ?? '';
            lc_lang_switch($lang);
        } else {
            $alias = $params[0] ?? '';
        }
        return $this->_categoryDetail($alias);
    }


    /**
     * Category detail: list category child + product list
     * @param  [string] $alias
     * @return [view]
     */
    private function _categoryDetail($alias)
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $filter_sort = request('filter_sort') ?? '';
        $filterArr = [
            'price_desc' => ['price', 'desc'],
            'price_asc' => ['price', 'asc'],
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        if (array_key_exists($filter_sort, $filterArr)) {
            $sortBy = $filterArr[$filter_sort][0];
            $sortOrder = $filterArr[$filter_sort][1];
        }

        $category = (new ShopCategory)->getDetail($alias, $type = 'alias');

        if ($category) {
            $products = (new ShopProduct)
                ->getProductToCategory([$category->id])
                ->setLimit(lc_config('product_list'))
                ->setPaginate()
                ->setSort([$sortBy, $sortOrder])
                ->getData();

            $subCategory = (new ShopCategory)
                ->setParent($category->id)
                ->setLimit(lc_config('item_list'))
                ->setPaginate()
                ->getData();

            lc_check_view($this->templatePath . '.screen.shop_product_list');
            return view($this->templatePath . '.screen.shop_product_list',
                array(
                    'title' => $category->title,
                    'description' => $category->description,
                    'keyword' => $category->keyword,
                    'products' => $products,
                    'subCategory' => $subCategory,
                    'layout_page' => 'shop_product_list',
                    'og_image' => asset($category->getImage()),
                    'filter_sort' => $filter_sort,
                )
            );
        } else {
            return $this->itemNotFound();
        }

    }

}

<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopBrand;
use App\Models\Front\ShopTax;
use App\Models\Front\ShopLanguage;
use App\Models\Front\ShopWeight;
use App\Models\Front\ShopLength;
use App\Models\Front\ShopProductAttribute;
use App\Models\Front\ShopProductBuild;
use App\Models\Front\ShopProductGroup;
use App\Models\Front\ShopProductImage;
use App\Models\Front\ShopSupplier;
use App\Models\Front\ShopProductDownload;
use App\Models\Front\ShopCurrency;
use App\Models\Front\ShopProductProperty;
use App\Models\Front\ShopCustomField;
use App\Models\Front\ShopCustomFieldDetail;
use App\Models\Admin\Product;
use App\Models\Admin\Category;
use App\Models\Front\ShopAttributePalette;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Helper\JsonResponse;
use App\Http\Resources\ProductCollection;
use Validator;
use Carbon\Carbon;

class ProductController extends Controller
{
    public $kinds;
    public $properties;
    public $attributeGroup;
    public $listWeight;
    public $listLength;
    public $categories;

    public function __construct(Request $request)
    {
        $this->listWeight      = ShopWeight::getListAll();
        $this->listLength      = ShopLength::getListAll();
        $this->attributeGroup  = ShopAttributeGroup::getListType();
        $this->kinds = [
            LC_PRODUCT_SINGLE => trans('product.kinds.single'),
            LC_PRODUCT_BUILD  => trans('product.kinds.build'),
            LC_PRODUCT_GROUP  => trans('product.kinds.group'),
        ];
        $this->properties = (new ShopProductProperty)->pluck('name', 'code')->toArray();
        $this->categories =  (new Category)->getTreeCategoriesAdmin();
    }

    public function index(Request $request)
    {
        $searchParams = $request->all();
        $data = (new Product)->getProductListAdmin($searchParams);
        return ProductCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Get min max price product
     * @return [type] [description]
     */
    public function getMaxPriceProduct($type)
    {
        $type = strtolower($type);
        $max = Product::gettableProduct()->max($type);
        $data = ['max' => $max];
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }

    /*
     * API show
     */
    public function show($id)
    {
        $product = (new Product)->getProductAdmin($id);
        if (!$product) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        return response()->json(new JsonResponse($product), Response::HTTP_OK);
    }

/**
 * Form create new item in admin
 * @return [type] [description]
 */
public function createProductBuild()
{
    $listProductSingle = (new Product)->getProductSelectAdmin(['kind' => [0]]);
    $data = [
        'title'                => trans('product.admin.add_new_title_build'),
        'kind'                 => lc_PRODUCT_BUILD,
        'title_description'    => trans('product.admin.add_new_des'),
        'icon'                 => 'fa fa-plus',
        'languages'            => $this->languages,
        'categories'           => $this->categories,
        'brands'               => (new ShopBrand)->getListAll(),
        'suppliers'              => (new ShopSupplier)->getListAll(),
        'taxs'                 => (new ShopTax)->getListAll(),
        'properties'            => $this->properties,
        'kinds'                => $this->kinds,
        'attributeGroup'       => $this->attributeGroup,
        'listProductSingle'    => $listProductSingle,
        'listWeight'           => $this->listWeight,
        'listLength'           => $this->listLength,
    ];
    return view($this->templatePathAdmin.'Product.add')
        ->with($data);
}


/**
 * Form create new item in admin
 * @return [type] [description]
 */
public function createProductGroup()
{
    $listProductSingle = (new Product)->getProductSelectAdmin(['kind' => [0]]);

    // html select product group
    $htmlSelectGroup = '<div class="select-product">';
    $htmlSelectGroup .= '<table width="100%"><tr><td width="80%"><select class="form-control rounded-0 productInGroup select2" data-placeholder="' . trans('product.admin.select_product_in_group') . '" style="width: 100%;" name="productInGroup[]" >';
    $htmlSelectGroup .= '';
    foreach ($listProductSingle as $k => $v) {
        $htmlSelectGroup .= '<option value="' . $k . '">' . $v['name'] . '</option>';
    }
    $htmlSelectGroup .= '</select></td><td><span title="Remove" class="btn btn-flat btn-danger removeproductInGroup"><i class="fa fa-times"></i></span></td></tr></table>';
    $htmlSelectGroup .= '</div>';
    //End select product group


    $data = [
        'title'                => trans('product.admin.add_new_title_group'),
        'kind'                 => lc_PRODUCT_GROUP,
        'title_description'    => trans('product.admin.add_new_des'),
        'icon'                 => 'fa fa-plus',
        'languages'            => $this->languages,
        'categories'           => $this->categories,
        'brands'               => (new ShopBrand)->getListAll(),
        'suppliers'            => (new ShopSupplier)->getListAll(),
        'taxs'                 => (new ShopTax)->getListAll(),
        'properties'            => $this->properties,
        'kinds'                => $this->kinds,
        'attributeGroup'       => $this->attributeGroup,
        'listProductSingle'    => $listProductSingle,
        'htmlSelectGroup'      => $htmlSelectGroup,
        'listWeight'           => $this->listWeight,
        'listLength'           => $this->listLength,
    ];

    return view($this->templatePathAdmin.'Product.add')
        ->with($data);
}


    /**
     * Post create new item in admin
     * @return [type] [description]
    */

    public function store(Request $request)
    {
        $data = $request->all();
        $data['descriptions'] = json_decode($data['descriptions']);
        $data['brand'] = json_decode($data['brand']);
        $data['supplier'] = json_decode($data['supplier']);
        $data['tax'] = json_decode($data['tax']);
        $data['currency'] = json_decode($data['currency']);
        $data['attribute'] = json_decode($data['attribute']);
        $data['category'] = json_decode($data['category']);
        array_walk_recursive($data['category'], function ($value, $key) use (&$category){
            $category[] = $value;
        }, $category);

        $category        = array_unique($category) ?? [];

        $data['date_promotion'] = json_decode($data['date_promotion']);

        $langFirst = array_key_first(lc_language_all($data['store_id'])->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions']->$langFirst->title;
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);
        switch ($data['kind']) {
            case LC_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'kind'                       => 'required',
                    'currency'                   => 'required',
                    'sort'                       => 'numeric|min:0',
                    'minimum'                    => 'numeric|min:0',
                    'descriptions.*.title'       => 'required|string|max:100',
                    'descriptions.*.keyword'     => 'nullable|string|max:100',
                    'descriptions.*.description' => 'nullable|string|max:100',
                    'descriptions.*.content'     => 'required|string',
                    'category'                   => 'required|not_in:0',
                    'images'                     => 'required_without:files',
                    'files'                      => 'required_without:images',
                    'store_id'                   => 'required',
                    // 'sub_image'                  => 'required',
                    // 'type_show_image_desc'       => 'required',
                    'sku'                        => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:shop_product,sku',
                    'alias'                      => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|unique:shop_product,alias',
                ];

                //Custom fields
                $customFields = (new ShopCustomField)->getCustomField($type = 'product');
                if ($customFields) {
                    foreach ($customFields as $field) {
                        if ($field->required) {
                            $arrValidation['fields.'.$field->code] = 'required';
                        }
                    }
                }

                $arrValidation = $this->validateAttribute($arrValidation,$data['store_id']);

                $arrMsg = [
                    'descriptions.*.title.required'    => trans('validation.required', ['attribute' => trans('product.name')]),
                    'descriptions.*.content.required' => trans('validation.required', ['attribute' => trans('product.content')]),
                    'category.required'               => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex'                       => trans('product.sku_validate'),
                    'sku.product_sku_unique'          => trans('product.sku_unique'),
                    'alias.regex'                     => trans('product.alias_validate'),
                    'alias.product_alias_unique'      => trans('product.alias_unique'),
                ];
                break;

            case LC_PRODUCT_BUILD: //product build
                $arrValidation = [
                    'kind'                       => 'required',
                    'sort'                       => 'numeric|min:0',
                    'minimum'                    => 'numeric|min:0',
                    'descriptions.*.name'        => 'required|string|max:100',
                    'descriptions.*.keyword'     => 'nullable|string|max:100',
                    'descriptions.*.description' => 'nullable|string|max:100',
                    'category'                   => 'required',
                    'image'                      => 'required',
                    'sku'                        => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique',
                    'alias'                      => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique',
                ];

                $arrValidation = $this->validateAttribute($arrValidation,$data['store_id']);

                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'category.required'            => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex'                    => trans('product.sku_validate'),
                    'sku.product_sku_unique'       => trans('product.sku_unique'),
                    'alias.regex'                  => trans('product.alias_validate'),
                    'alias.product_alias_unique'   => trans('product.alias_unique'),
                ];
                break;

            case LC_PRODUCT_GROUP: //product group
                $arrValidation = [
                    'kind'                       => 'required',
                    'productInGroup'             => 'required',
                    'sku'                        => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique',
                    'alias'                      => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique',
                    'sort'                       => 'numeric|min:0',
                    'category'                   => 'required',
                    'image'                       => 'required',
                    'descriptions.*.name'        => 'required|string|max:200',
                    'descriptions.*.keyword'     => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                ];
                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'sku.regex'                    => trans('product.sku_validate'),
                    'category.required'            => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.product_sku_unique'       => trans('product.sku_unique'),
                    'alias.regex'                  => trans('product.alias_validate'),
                    'alias.product_alias_unique'   => trans('product.alias_unique'),
                ];
                break;

            default:
                $arrValidation = [
                    'kind' => 'required',
                ];
                break;
        }

        $validator = Validator::make($data, $arrValidation, $arrMsg ?? []);
        if ($validator->fails()) {
            return response()->json(new JsonResponse([],$validator->messages()), Response::HTTP_BAD_REQUEST);
        }
        $attribute       = $data['attribute'] ?? [];
        $descriptions    = $data['descriptions'];
        $productInGroup  = $data['productInGroup'] ?? [];
        $productBuild    = $data['hotSpots'] ?? [];
        /* UPLOAD IMAGE */
        if(isset($data['files'])){
            $data['files'] = is_array($data['files']) ? $data['files'] : [$data['files']];
            foreach ($data['files'] as $key => $image) {
                if($request->hasFile('files.'.$key)){
                    $path = 'public/product/';
                    $fileName = $request->file('files.'.$key)->hashName();
                    $request->file('files.'.$key)->storeAs(
                        $path,$fileName
                    );
                    if (isset($data['images']) && is_array($data['images'])) {
                        array_push($data['images'], LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX.'/getFile?disk=product&path='.$fileName);
                    }else{
                        $data['images'] = [LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX.'/getFile?disk=product&path='.$fileName];
                    }
                }
            }
        }
        // $subImages       = $data['sub_image'] ?? '';
        // $type_show_image_desc = $data['type_show_image_desc'] ?? '';
        // $downloadPath    = $data['download_path'] ?? '';

        $currency = ShopCurrency::find($data['currency']->value);

        $cost = $data['cost'] / $currency->exchange_rate;
        $price = $data['price'] / $currency->exchange_rate;

        $dataInsert = [
            'brand_id'       => $data['brand']->value ?? 0,
            'supplier_id'    => $data['supplier']->value ?? 0,
            'category_store_id' => $data['category_store_id'] ?? 0,
            'sku'            => $data['sku'],
            'cost'           => $cost ?? 0,
            'price'          => $price ?? 0,
            'stock'          => $data['stock'] ?? 0,
            'weight_class'   => $data['weight_class'] ?? '',
            'length_class'   => $data['length_class'] ?? '',
            'weight'         => $data['weight'] ?? 0,
            'height'         => $data['height'] ?? 0,
            'length'         => $data['length'] ?? 0,
            'width'          => $data['width'] ?? 0,
            'kind'           => $data['kind'] ?? LC_PRODUCT_SINGLE,
            'alias'          => $data['alias'],
            'property'       => $data['property'] ?? LC_PROPERTY_PHYSICAL,
            'image'          => (is_array($data['images']) ? implode(',',$data['images']) : $data['images'] ) ?? $product->image,
            'tax_id'         => $data['tax']->value ?? 0,
            'status'         => (!empty($data['status']) ? 1 : 0),
            'sort'           => (int) $data['sort'],
            'minimum'        => (int) ($data['minimum'] ?? 0),
            'currency'       => $data['currency']->value,
            'store_id'       => $data['store_id'],
        ];

        if(!empty($data['date_available'])) {
            $dataInsert['date_available'] = $data['date_available'];
        }
        //insert product
        $product = Product::createProductAdmin($dataInsert);

        //Promoton price
        if (isset($data['price_promotion']) && isset($data['date_promotion']) && $data['price_promotion'] > 0 && in_array($data['kind'], [LC_PRODUCT_SINGLE, LC_PRODUCT_BUILD])) {
            $arrPromotion['price_promotion'] = $data['price_promotion'];
            $arrPromotion['date_start'] = $data['date_promotion']->start ? Carbon::parse($data['date_promotion']->start)->format('Y-m-d H:i:s') : null;
            $arrPromotion['date_end'] = $data['date_promotion']->end ? Carbon::parse($data['date_promotion']->end)->format('Y-m-d H:i:s') : null;
            $product->promotionPrice()->create($arrPromotion);
        }

        //Insert category
        if ($category) {
            $category = $category;
            $product->categories()->attach($category);
        }

        //Insert group
        if ($productInGroup && $data['kind'] == LC_PRODUCT_GROUP) {
            $arrDataGroup = [];
            foreach ($productInGroup as $pID) {
                if ((int) $pID) {
                    $arrDataGroup[$pID] = new ShopProductGroup(['product_id' => $pID]);
                }
            }
            $product->groups()->saveMany($arrDataGroup);
        }

        //Insert Build
        if ($productBuild && $data['kind'] == LC_PRODUCT_BUILD) {
            $arrDataBuild = [];
            foreach ($productBuild as $key => $value) {
                $arrDataBuild[] = new ShopProductBuild($value);
            }
            $product->builds()->saveMany($arrDataBuild);
        }

        //Insert attribute
        if ($attribute && $data['kind'] == LC_PRODUCT_SINGLE) {
            $arrDataAtt = [];
            foreach ($data['attribute'] as $group => $rowGroup) {
                if ($rowGroup) {
                    foreach ($rowGroup->values as $key => $value) {
                        if ($value) {
                            $arrDataPalette = [];
                            $images = '';
                            if (isset($value->files)) {
                                $images = implode(',', $value->files);
                            }
                            $add_price = $value->add_price / $currency->exchange_rate;
                            $arrDataAtt =  [
                                                'name' => $value->name,
                                                'add_price' => $add_price,
                                                'attribute_group_id' => $rowGroup->id,
                                                'images' => $images,
                                                'product_id' => $product->id,
                                                'parent'  => 0,
                                            ];
                            $justProdAttribute = ShopProductAttribute::create($arrDataAtt);

                            if (isset($value->children)) {
                                $children = $value->children;
                                foreach ($children as $keychildren => $valuechildren) {
                                    $images = '';
                                    if (isset($valuechildren->files)) {
                                        $images = implode(',', $valuechildren->files);
                                    }
                                    $add_price = $valuechildren->add_price / $currency->exchange_rate;
                                    $arrDataChildren =  [
                                                'name' => $valuechildren->name,
                                                'add_price' => $add_price,
                                                'attribute_group_id' => $rowGroup->child_id,
                                                'images' => $images,
                                                'product_id' => $product->id,
                                                'parent'  => $justProdAttribute->id,
                                            ];

                                    $justProdAttributeChild = ShopProductAttribute::create($arrDataChildren);

                                    if (isset($valuechildren->palette)) {
                                        $childPalette = $valuechildren->palette;

                                        foreach ($childPalette as $childkeypalette => $childvaluepalette) {
                                            $arrDataChildPalette[] = [
                                                'name' => $childvaluepalette->name,
                                                'type' => $childvaluepalette->type,
                                                'hex' => $childvaluepalette->hex,
                                                'attribute_id' => $justProdAttributeChild->id,
                                                'product_id' => $product->id,
                                                'active' => $childvaluepalette->active,
                                            ];
                                        }
                                        ShopAttributePalette::insert($arrDataChildPalette);
                                    };
                                }
                            }

                            if (isset($value->palette)) {
                                $palette = $value->palette;

                                foreach ($palette as $keypalette => $valuepalette) {
                                    $arrDataPalette[] = [
                                        'name' => $valuepalette->name,
                                        'type' => $valuepalette->type,
                                        'hex' => $valuepalette->hex,
                                        'attribute_id' => $justProdAttribute->id,
                                        'product_id' => $product->id,
                                        'active' => $valuepalette->active,
                                    ];
                                }
                                ShopAttributePalette::insert($arrDataPalette);
                            };
                        }
                    }

                }

            }
        }

        //Insert path download
        if (!empty($data['property']) && $data['property'] == LC_PROPERTY_DOWNLOAD && $downloadPath) {
            (new ShopProductDownload)->insert(['product_id' => $product->id, 'path' => $downloadPath]);
        }

        //Insert custom fields
        if (!empty($data['fields'])) {
            $dataField = [];
            foreach ($data['fields'] as $key => $value) {
                $field = (new ShopCustomField)->where('code', $key)->where('type', 'product')->first();
                if ($field) {
                    $dataField[] = [
                        'custom_field_id' => $field->id,
                        'rel_id' => $product->id,
                        'text' => trim($value),
                    ];
                }
            }
            if ($dataField) {
                (new ShopCustomFieldDetail)->insert($dataField);
            }
        }


        //Insert description
        $dataDes = [];
        $languages = ShopLanguage::getListActive($data['store_id']);
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'product_id'  => $product->id,
                'lang'        => $code,
                'name'        => $descriptions->$code->title,
                'keyword'     => implode(',',$descriptions->$code->keyword),
                'description' => $descriptions->$code->description,
                'content'     => $descriptions->$code->content ?? '',
            ];
        }
        Product::insertDescriptionAdmin($dataDes);

        //Insert sub mages
        // if ($subImages && in_array($data['kind'], [LC_PRODUCT_SINGLE, LC_PRODUCT_BUILD])) {
        //     $SubImages = new ShopProductImage(['image'=>$subImages,'type_show'=>$type_show_image_desc]);
        //     $product->img()->save($SubImages);
        // }

        lc_clear_cache('cache_product');

        return response()->json(new JsonResponse([],trans('product.admin.create_success')), Response::HTTP_OK);

    }

    /**
     * Put update item in admin
     * @return [type] [description]
    */
    public function update(Request $request,$id)
    {
        $product = (new Product)->getProductAdmin($id);
        if ($product === null) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        $data['descriptions'] = json_decode($data['descriptions']);
        $data['brand'] = json_decode($data['brand']);
        $data['supplier'] = json_decode($data['supplier']);
        $data['tax'] = json_decode($data['tax']);
        $data['currency'] = json_decode($data['currency']);
        $data['attribute'] = json_decode($data['attribute']);
        $data['length_class'] = json_decode($data['length_class'])->value ?? $product->length_class;
        $data['weight_class'] = json_decode($data['weight_class'])->value ?? $product->weight_class;
        /// detach category
        $data['category'] = json_decode($data['category']);

        array_walk_recursive($data['category'], function ($value, $key) use (&$category){
            $category[] = $value;
        }, $category);

        $category        = array_unique($category) ?? [];

        $data['date_promotion'] = json_decode($data['date_promotion']);

        $langFirst = array_key_first(lc_language_all($product->store_id)->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions']->$langFirst->title;
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        switch ($product['kind']) {
            case LC_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'sort' => 'numeric|min:0',
                    'minimum' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                    'descriptions.*.content' => 'required|string',
                    'category' => 'required',
                    'currency' => 'required',
                    'images' => 'required_without:files',
                    'files' => 'required_without:images',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:shop_product,sku,'.$id,
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|unique:shop_product,alias,'.$id,
                ];

                //Custom fields
                $customFields = (new ShopCustomField)->getCustomField($type = 'product');
                if ($customFields) {
                    foreach ($customFields as $field) {
                        if ($field->required) {
                            $arrValidation['fields.'.$field->code] = 'required';
                        }
                    }
                }

                $arrValidation = $this->validateAttribute($arrValidation,$product->store_id);

                $arrMsg = [
                    'descriptions.*.name.required'    => trans('validation.required', ['attribute' => trans('product.name')]),
                    'descriptions.*.content.required' => trans('validation.required', ['attribute' => trans('product.content')]),
                    'category.required'               => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex'                       => trans('product.sku_validate'),
                    'sku.product_sku_unique'          => trans('product.sku_unique'),
                    'alias.regex'                     => trans('product.alias_validate'),
                    'alias.product_alias_unique'      => trans('product.alias_unique'),
                ];
                break;
            case LC_PRODUCT_BUILD: //product build
                $arrValidation = [
                    'sort' => 'numeric|min:0',
                    'minimum' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                    'category' => 'required',
                    'image' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique:'.$id,
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique:'.$id,
                ];

                $arrValidation = $this->validateAttribute($arrValidation,$product->store_id);

                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'category.required'            => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex'                    => trans('product.sku_validate'),
                    'sku.product_sku_unique'       => trans('product.sku_unique'),
                    'alias.regex'                  => trans('product.alias_validate'),
                    'alias.product_alias_unique'   => trans('product.alias_unique'),
                ];
                break;

            case LC_PRODUCT_GROUP: //product group
                $arrValidation = [
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique:'.$id,
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique:'.$id,
                    'productInGroup' => 'required',
                    'category' => 'required',
                    'image' => 'required',
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                ];
                $arrMsg = [
                    'sku.regex'                    => trans('product.sku_validate'),
                    'sku.product_sku_unique'       => trans('product.sku_unique'),
                    'category.required'            => trans('validation.required', ['attribute' => trans('product.category')]),
                    'alias.regex'                  => trans('product.alias_validate'),
                    'alias.product_alias_unique'   => trans('product.alias_unique'),
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                ];
                break;

            default:
                break;
        }

        $validator = Validator::make($data, $arrValidation, $arrMsg ?? []);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([],$validator->messages()), Response::HTTP_BAD_REQUEST);
        }
        //Edit

        $attribute       = $data['attribute'] ?? [];
        $productInGroup  = $data['productInGroup'] ?? [];
        $descriptions    = $data['descriptions'];
        $productBuild    = $data['hotSpots'] ?? [];
        $subImages       = $data['sub_image'] ?? '';
        // $type_show_image_desc = $data['type_show_image_desc'] ?? '';
        $downloadPath    = $data['download_path'] ?? '';
        /* UPLOAD IMAGE */


        if(isset($data['files'])){
            $data['files'] = is_array($data['files']) ? $data['files'] : [$data['files']];
            foreach ($data['files'] as $key => $image) {
                if($request->hasFile('files.'.$key)){
                    $path = 'public/product/';
                    $fileName = $request->file('files.'.$key)->hashName();
                    $request->file('files.'.$key)->storeAs(
                        $path,$fileName
                    );
                    if (isset($data['images']) && is_array($data['images'])) {
                        array_push($data['images'], LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX.'/getFile?disk=product&path='.$fileName);
                    }else{
                        $data['images'] = [LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX.'/getFile?disk=product&path='.$fileName];
                    }
                }
            }
        }

        $currency = ShopCurrency::find($data['currency']->value);
        $cost = $data['cost'] / $currency->exchange_rate;
        $price = $data['price'] / $currency->exchange_rate;

        $dataUpdate = [
            'image'        => (is_array($data['images']) ? implode(',',$data['images']) : $data['images'] ) ?? $product->image,
            'tax_id'       => $data['tax']->value ?? $product->tax_id,
            'brand_id'       => $data['brand']->value ?? $product->brand_id,
            'supplier_id'    => $data['supplier']->value ?? $product->supplier_id,
            'category_store_id'     => $data['category_store_id'] ?? 0,
            'price'        => $price ?? $product->price,
            'cost'         => $cost ?? $product->cost,
            'stock'        => $data['stock'] ?? $product->stock,
            'weight_class' => $data['weight_class'],
            'length_class' => $data['length_class'],
            'weight'       => $data['weight'] ?? $product->weight,
            'height'       => $data['height'] ?? $product->height,
            'length'       => $data['length'] ?? $product->length,
            'width'        => $data['width'] ?? $product->width,
            'property'     => $data['property'] ?? $product->property,
            'sku'          => $data['sku'],
            'alias'        => $data['alias'],
            'status'       => (!empty($data['status']) ? 1 : 0),
            'sort'         => (int) $data['sort'],
            'minimum'      => (int) ($data['minimum'] ?? 0),
            'currency'     => $data['currency']->value ?? $product->currency,
            'store_id'     => $product->store_id,
        ];


        if (!empty($data['date_available'])) {
            $dataUpdate['date_available'] = $data['date_available'];
        }
        $product->update($dataUpdate);


        //Update custom field
        if (!empty($data['fields'])) {
            (new ShopCustomFieldDetail)
                ->join(LC_DB_PREFIX.'shop_custom_field', LC_DB_PREFIX.'shop_custom_field.id', LC_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->select('code', 'name', 'text')
                ->where(LC_DB_PREFIX.'shop_custom_field_detail.rel_id', $product->id)
                ->where(LC_DB_PREFIX.'shop_custom_field.type', 'product')
                ->delete();

            $dataField = [];
            foreach ($data['fields'] as $key => $value) {
                $field = (new ShopCustomField)->where('code', $key)->where('type', 'product')->first();
                if ($field) {
                    $dataField[] = [
                        'custom_field_id' => $field->id,
                        'rel_id' => $product->id,
                        'text' => trim($value),
                    ];
                }
            }
            if ($dataField) {
                (new ShopCustomFieldDetail)->insert($dataField);
            }
        }

        //Promoton price
        if (isset($data['price_promotion']) && isset($data['date_promotion']) && $data['price_promotion'] > 0 && in_array($product['kind'], [LC_PRODUCT_SINGLE, LC_PRODUCT_BUILD])) {
            $product->promotionPrice()->delete();
            $arrPromotion['price_promotion'] = $data['price_promotion'];
            $arrPromotion['date_start'] = $data['date_promotion']->start ? Carbon::parse($data['date_promotion']->start)->format('Y-m-d H:i:s') : null;
            $arrPromotion['date_end'] = $data['date_promotion']->end ? Carbon::parse($data['date_promotion']->end)->format('Y-m-d H:i:s') : null;
            $product->promotionPrice()->create($arrPromotion);
        }

        //Insert description
        $product->descriptions()->delete();

        $dataDes = [];
        $languages = ShopLanguage::getListActive($product->store_id);
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'product_id'  => $product->id,
                'lang'        => $code,
                'name'        => $descriptions->$code->title,
                'keyword'     => property_exists($descriptions->$code,'keyword') ? implode(',',$descriptions->$code->keyword) : '',
                'description' => $descriptions->$code->description ?? null,
                'content'     => $descriptions->$code->content ?? '',
            ];
        }
        Product::insertDescriptionAdmin($dataDes);

        $product->categories()->detach();
        if (count($category)) {
            $product->categories()->attach($category);
        }

        //Update group
        if ($product['kind'] == LC_PRODUCT_GROUP) {
            $product->groups()->delete();
            if (count($productInGroup)) {
                $arrDataGroup = [];
                foreach ($productInGroup as $pID) {
                    if ((int) $pID) {
                        $arrDataGroup[$pID] = new ShopProductGroup(['product_id' => $pID]);
                    }
                }
                $product->groups()->saveMany($arrDataGroup);
            }

        }

        //Update Build
        if ($product['kind'] == LC_PRODUCT_BUILD) {
            $product->builds()->delete();
            if ($productBuild && $product['kind'] == LC_PRODUCT_BUILD) {
                $arrDataBuild = [];
                foreach ($productBuild as $key => $value) {
                    $arrDataBuild[] = new ShopProductBuild($value);
                }
                $product->builds()->saveMany($arrDataBuild);
            }
        }

        //Update path download
        if ($product['property'] == LC_PROPERTY_DOWNLOAD && $downloadPath) {
            (new ShopProductDownload)->where('product_id', $product->id)->delete();
            (new ShopProductDownload)->insert(['product_id' => $product->id, 'path' => $downloadPath]);
        }


        //Update attribute
        if ($attribute && $data['kind'] == LC_PRODUCT_SINGLE) {
            $product->attributes()->delete();
            $product->palette()->delete();
            $arrDataAtt = [];
            foreach ($data['attribute'] as $group => $rowGroup) {
                if ($rowGroup) {
                    foreach ($rowGroup->values as $key => $value) {
                        if ($value) {
                            $arrDataPalette = [];
                            $images = '';
                            if (isset($value->files)) {
                                $images = implode(',', $value->files);
                            }
                            $add_price = $value->add_price / $currency->exchange_rate;
                            $arrDataAtt =  [
                                                'name' => $value->name,
                                                'add_price' => $add_price,
                                                'attribute_group_id' => $rowGroup->id,
                                                'images' => $images,
                                                'product_id' => $product->id,
                                                'parent'  => 0,
                                            ];
                            $justProdAttribute = ShopProductAttribute::create($arrDataAtt);

                            if (isset($value->children)) {
                                $children = $value->children;
                                foreach ($children as $keychildren => $valuechildren) {
                                    $images = '';
                                    if (isset($valuechildren->files)) {
                                        $images = implode(',', $valuechildren->files);
                                    }
                                    $add_price = $valuechildren->add_price / $currency->exchange_rate;
                                    $arrDataChildren =  [
                                                'name' => $valuechildren->name,
                                                'add_price' => $add_price,
                                                'attribute_group_id' => $rowGroup->child_id,
                                                'images' => $images,
                                                'product_id' => $product->id,
                                                'parent'  => $justProdAttribute->id,
                                            ];

                                    $justProdAttributeChild = ShopProductAttribute::create($arrDataChildren);

                                    if (isset($valuechildren->palette)) {
                                        $childPalette = $valuechildren->palette;

                                        foreach ($childPalette as $childkeypalette => $childvaluepalette) {
                                            $arrDataChildPalette[] = [
                                                'name' => $childvaluepalette->name,
                                                'type' => $childvaluepalette->type,
                                                'hex' => $childvaluepalette->hex,
                                                'attribute_id' => $justProdAttributeChild->id,
                                                'product_id' => $product->id,
                                                'active' => $childvaluepalette->active
                                            ];
                                        }
                                        if(isset($arrDataChildPalette)){
                                            ShopAttributePalette::insert($arrDataChildPalette);
                                        }
                                    };
                                }
                            }

                            if (isset($value->palette)) {
                                $palette = $value->palette;

                                foreach ($palette as $keypalette => $valuepalette) {
                                    $arrDataPalette[] = [
                                        'name' => $valuepalette->name,
                                        'type' => $valuepalette->type,
                                        'hex' => $valuepalette->hex,
                                        'attribute_id' => $justProdAttribute->id,
                                        'product_id' => $product->id,
                                        'active' => $valuepalette->active
                                    ];
                                }
                                ShopAttributePalette::insert($arrDataPalette);
                            };
                        }
                    }

                }

            }
        }

        //Update sub mages
        if ($subImages && in_array($product['kind'], [LC_PRODUCT_SINGLE, LC_PRODUCT_BUILD])) {
            $product->img()->delete();
            $SubImages = new ShopProductImage(['image'=>$subImages,'type_show'=>$type_show_image_desc]);
            $product->img()->save($SubImages);
        }

        lc_clear_cache('cache_product');

        return response()->json(new JsonResponse(['message' => trans('product.admin.edit_success')]), Response::HTTP_OK);
    }

    /*
        Delete list Item
        Need mothod destroy to boot deleting in model
    */
    public function destroy($ids)
    {
        if (!request()->ajax()) {
            return response()->json(new JsonResponse([],trans('admin.method_not_allow')), Response::HTTP_BAD_REQUEST);
        } else {
            $arrID = explode(',', $ids);
            $arrID = array_filter($arrID);
            $arrCantDelete = [];
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if(!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
                if (ShopProductBuild::where('product_id', $id)->first() || ShopProductGroup::where('product_id', $id)->first()) {
                    $arrCantDelete[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(new JsonResponse([],trans('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)), Response::HTTP_BAD_REQUEST);
            } elseif (count($arrCantDelete)) {
                return response()->json(new JsonResponse([],trans('product.admin.cant_remove_child') . ': ' . json_encode($arrCantDelete)),Response::HTTP_BAD_REQUEST);
            }else {
                Product::destroy($arrID);
                lc_clear_cache('cache_product');
                return response()->json(new JsonResponse([],trans('admin.delete_success')), Response::HTTP_OK);
            }

        }
    }
    /**
     * Validate attribute product
     */
    public function validateAttribute(array $arrValidation,$storeId) {
        if (lc_config('product_brand',$storeId)) {
            if (lc_config('product_brand_required',$storeId)) {
                $arrValidation['brand_id'] = 'required|numeric';
            } else {
                $arrValidation['brand_id'] = 'nullable|numeric';
            }
        }

        if (lc_config('product_supplier',$storeId)) {
            if (lc_config('product_supplier_required',$storeId)) {
                $arrValidation['supplier_id'] = 'required';
            } else {
                $arrValidation['supplier_id'] = 'nullable';
            }
        }

        if (lc_config('product_price',$storeId)) {
            if (lc_config('product_price_required',$storeId)) {
                $arrValidation['price'] = 'required|numeric|min:0';
            } else {
                $arrValidation['price'] = 'nullable|numeric|min:0';
            }
        }

        if (lc_config('product_cost',$storeId)) {
            if (lc_config('product_cost_required',$storeId)) {
                $arrValidation['cost'] = 'required|numeric|min:0';
            } else {
                $arrValidation['cost'] = 'nullable|numeric|min:0';
            }
        }

        if (lc_config('product_promotion',$storeId)) {
            if (lc_config('product_promotion_required',$storeId)) {
                $arrValidation['price_promotion'] = 'required|numeric|min:0';
            } else {
                $arrValidation['price_promotion'] = 'nullable|numeric|min:0';
            }
        }

        if (lc_config('product_stock',$storeId)) {
            if (lc_config('product_stock_required',$storeId)) {
                $arrValidation['stock'] = 'required|numeric';
            } else {
                $arrValidation['stock'] = 'nullable|numeric';
            }
        }

        if (lc_config('product_property',$storeId)) {
            if (lc_config('product_property_required',$storeId)) {
                $arrValidation['property'] = 'required|string';
            } else {
                $arrValidation['property'] = 'nullable|string';
            }
        }

        if (lc_config('product_available',$storeId)) {
            if (lc_config('product_available_required',$storeId)) {
                $arrValidation['date_available'] = 'required|date';
            } else {
                $arrValidation['date_available'] = 'nullable|date';
            }
        }

        if (lc_config('product_weight',$storeId)) {
            if (lc_config('product_weight_required',$storeId)) {
                $arrValidation['weight'] = 'required|numeric';
                $arrValidation['weight_class'] = 'required|string';
            } else {
                $arrValidation['weight'] = 'nullable|numeric';
                $arrValidation['weight_class'] = 'nullable|string';
            }
        }

        if (lc_config('product_length',$storeId)) {
            if (lc_config('product_length_required',$storeId)) {
                $arrValidation['length_class'] = 'required|string';
                $arrValidation['length'] = 'required|numeric|min:0';
                $arrValidation['width'] = 'required|numeric|min:0';
                $arrValidation['height'] = 'required|numeric|min:0';
            } else {
                $arrValidation['length_class'] = 'nullable|string';
                $arrValidation['length'] = 'nullable|numeric|min:0';
                $arrValidation['width'] = 'nullable|numeric|min:0';
                $arrValidation['height'] = 'nullable|numeric|min:0';
            }
        }
        return $arrValidation;
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return (new Product)->getProductAdmin($id);
    }
    /**
     * update top product
     */
    public function updateTopProduct(Request $request,$id)
    {
        $product = (new Product)->getProductAdmin($id);
        if ($product === null) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $product->top = $request->top;
        $product->save();

        return response()->json(new JsonResponse(['message' => trans('product.admin.edit_success')]), Response::HTTP_OK);
    }
}

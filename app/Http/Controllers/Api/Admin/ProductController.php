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
    public $languages;
    public $kinds;
    public $properties;
    public $attributeGroup;
    public $listWeight;
    public $listLength;
    public $categories;

    public function __construct()
    {
        $this->languages       = ShopLanguage::getListActive();
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
        $dataSearch = $request->all();
        $data = (new Product)->getProductListAdmin($dataSearch);
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
            return response()->json(new JsonResponse([],'Resource not found'), Response::HTTP_NOT_FOUND);
        }

        // $data = [
        //     'title'                => trans('product.admin.add_new_title'),
        //     'kind'                     => lc_PRODUCT_SINGLE,
        //     'title_description'    => trans('product.admin.add_new_des'),
        //     'icon'                 => 'fa fa-plus',
        //     'languages'            => $this->languages,
        //     'categories'           => $this->categories,
        //     'brands'               => (new ShopBrand)->getListAll(),
        //     'suppliers'            => (new ShopSupplier)->getListAll(),
        //     'taxs'                 => (new ShopTax)->getListAll(),
        //     'properties'           => $this->properties,
        //     'kinds'                => $this->kinds,
        //     'attributeGroup'       => $this->attributeGroup,
        //     'listWeight'           => $this->listWeight,
        //     'listLength'           => $this->listLength,
        //     'customFields'         => (new ShopCustomField)->getCustomField($type = 'product'),
        // ];

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
        $data['attribute'] = json_decode($data['attribute']);
        $data['category'] = json_decode($data['category']);
        $data['category'] = is_array($data['category']) ? end($data['category']) : $data['category'];
        $data['date_promotion'] = json_decode($data['date_promotion']);

        $langFirst = array_key_first(lc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions']->$langFirst->title;
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        switch ($data['kind']) {
            case LC_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'kind'                       => 'required',
                    'sort'                       => 'numeric|min:0',
                    'minimum'                    => 'numeric|min:0',
                    'descriptions.*.title'        => 'required|string|max:100',
                    'descriptions.*.keyword'     => 'nullable|string|max:100',
                    'descriptions.*.description' => 'nullable|string|max:100',
                    'descriptions.*.content'     => 'required|string',
                    'category'                   => 'required|not_in:0',
                    'image'                      => 'required',
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

                $arrValidation = $this->validateAttribute($arrValidation);
                
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

                $arrValidation = $this->validateAttribute($arrValidation);

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
        $category        = $data['category'] ?? null;
        $attribute       = $data['attribute'] ?? [];
        $descriptions    = $data['descriptions'];
        $productInGroup  = $data['productInGroup'] ?? [];
        $productBuild    = $data['hotSpots'] ?? [];
        /* UPLOAD IMAGE */
        if($request->hasFile('image')){
            $path = 'product/';
            $fileName = $request->file('image')->hashName();
            $request->file('image')->storeAs(
                $path,$fileName
            );
            $data['image'] = LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX.'/getFile?disk=product&path='.$fileName;
        }
        // $subImages       = $data['sub_image'] ?? '';
        // $type_show_image_desc = $data['type_show_image_desc'] ?? '';
        // $downloadPath    = $data['download_path'] ?? '';
        $dataInsert = [
            'brand_id'       => $data['brand']->value ?? 0,
            'supplier_id'    => $data['supplier']->value ?? 0,
            'category_store_id' => $data['category_store_id'] ?? 0,
            'price'          => $data['price'] ?? 0,
            'sku'            => $data['sku'],
            'cost'           => $data['cost'] ?? 0,
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
            'image'          => $data['image'] ?? '',
            'tax_id'         => $data['tax']->value ?? 0,
            'status'         => (!empty($data['status']) ? 1 : 0),
            'sort'           => (int) $data['sort'],
            'minimum'        => (int) ($data['minimum'] ?? 0),
            'store_id'       => session('adminStoreId'),
        ];

        if(!empty($data['date_available'])) {
            $dataInsert['date_available'] = $data['date_available'];
        }
        //insert product
        $product = Product::createProductAdmin($dataInsert);

        //Promoton price
        if (isset($data['price_promotion']) && $data['price_promotion'] > 0 && in_array($data['kind'], [LC_PRODUCT_SINGLE, LC_PRODUCT_BUILD])) {
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
        if ($data['attribute'] && $data['kind'] == LC_PRODUCT_SINGLE) {
            $arrDataAtt = [];
            foreach ($data['attribute'] as $group => $rowGroup) {
                if ($rowGroup) {
                    foreach ($rowGroup->values as $key => $nameAtt) {
                        if ($nameAtt) {
                            $arrDataPalette = [];
                            $images = '';
                            if (is_array($rowGroup->values[$key]) && array_key_exists('files', $rowGroup->values[$key])) {
                                $images = implode(',', $rowGroup->values[$key]->files);
                            }
                            $arrDataAtt =  [
                                                'name' => $nameAtt->name, 
                                                'add_price' => $rowGroup->values[$key]->price,
                                                'attribute_group_id' => $rowGroup->id,
                                                'images' => $images,
                                                'product_id' => $product->id
                                            ];
                            $justProdAttribute = ShopProductAttribute::create($arrDataAtt);
                            if (is_array($rowGroup->values[$key]) && array_key_exists('palette', $rowGroup->values[$key])) {
                                $palette = $rowGroup->values[$key]->palette;

                                foreach ($palette as $keypalette => $valuepalette) {
                                    $arrDataPalette[] = [
                                        'name' => $valuepalette->name,
                                        'type' => $valuepalette->type,
                                        'hex' => $valuepalette->hex,
                                        'attribute_id' => $justProdAttribute->id,
                                        'product_id' => $product->id
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
        $languages = $this->languages;
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

    /*
    * Form edit
    */
    public function edit($id)
    {
        $product = (new Product)->getProductAdmin($id);

        if ($product === null) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        // $transparent_black = array(0, 0, 0, 0);
        // $img = Image::make(public_path($product->image))->greyscale()->trim('top-right', null, 25, 50)->contrast(100)->contrast(100)->contrast(100)->contrast(100)->save(public_path('data/product/bar.png'));
        // $palette = Palette::fromFilename(public_path($product->image));
        // $topFive = $palette->getMostUsedColors(5);
        // foreach($topFive as $color => $count) {
        //     $colors[] = Color::fromIntToHex($color);
        // }
        // dd($colors);

        $listProductSingle = (new Product)->getProductSelectAdmin(['kind' => [lc_PRODUCT_SINGLE]]);

        $data = [
            'title'                => trans('product.admin.edit'),
            'subTitle'             => '',
            'title_description'    => '',
            'icon'                 => 'fa fa-edit',
            'languages'            => $this->languages,
            'product'              => $product,
            'categories'           => $this->categories,
            'brands'               => (new ShopBrand)->getListAll(),
            'suppliers'            => (new ShopSupplier)->getListAll(),
            'taxs'                 => (new ShopTax)->getListAll(),
            'properties'           => $this->properties,
            'kinds'                => $this->kinds,
            'attributeGroup'       => $this->attributeGroup,
            'listProductSingle'    => $listProductSingle,
            'listWeight'           => $this->listWeight,
            'listLength'           => $this->listLength,

        ];

        //Only prduct single have custom field
        if ($product->kind == lc_PRODUCT_SINGLE) {
            $data['customFields'] = (new ShopCustomField)->getCustomField($type = 'product');
        } else {
            $data['customFields'] = [];
        }
        return view($this->templatePathAdmin.'Product.edit')
            ->with($data);
    }

    /*
    * update status
    */
    public function postEdit($id)
    {
        $product = (new Product)->getProductAdmin($id);
        if ($product === null) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = request()->all();
        $langFirst = array_key_first(lc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['name'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        switch ($product['kind']) {
            case lc_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'sort' => 'numeric|min:0',
                    'minimum' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                    'descriptions.*.content' => 'required|string',
                    'category' => 'required',
                    'image' => 'required',
                    'sub_image' => 'required',
                    'type_show_image_desc' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique:'.$id,
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique:'.$id,
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

                $arrValidation = $this->validateAttribute($arrValidation);

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
            case lc_PRODUCT_BUILD: //product build
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

                $arrValidation = $this->validateAttribute($arrValidation);
                
                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'category.required'            => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex'                    => trans('product.sku_validate'),
                    'sku.product_sku_unique'       => trans('product.sku_unique'),
                    'alias.regex'                  => trans('product.alias_validate'),
                    'alias.product_alias_unique'   => trans('product.alias_unique'),
                ];
                break;

            case lc_PRODUCT_GROUP: //product group
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit

        $category        = $data['category'] ?? [];
        $attribute       = $data['attribute'] ?? [];
        $productInGroup  = $data['productInGroup'] ?? [];
        $productBuild    = $data['hotSpots'] ?? [];
        $subImages       = $data['sub_image'] ?? '';
        $type_show_image_desc = $data['type_show_image_desc'] ?? '';
        $downloadPath    = $data['download_path'] ?? '';
        $dataUpdate = [
            'image'        => $data['image'] ?? '',
            'tax_id'       => $data['tax_id'] ?? 0,
            'brand_id'     => $data['brand_id'] ?? 0,
            'supplier_id'  => $data['supplier_id'] ?? 0,
            'category_store_id'     => $data['category_store_id'] ?? 0,
            'price'        => $data['price'] ?? 0,
            'cost'         => $data['cost'] ?? 0,
            'stock'        => $data['stock'] ?? 0,
            'weight_class' => $data['weight_class'] ?? '',
            'length_class' => $data['length_class'] ?? '',
            'weight'       => $data['weight'] ?? 0,
            'height'       => $data['height'] ?? 0,
            'length'       => $data['length'] ?? 0,
            'width'        => $data['width'] ?? 0,
            'property'     => $data['property'] ?? lc_PROPERTY_PHYSICAL,
            'sku'          => $data['sku'],
            'alias'        => $data['alias'],
            'status'       => (!empty($data['status']) ? 1 : 0),
            'sort'         => (int) $data['sort'],
            'minimum'      => (int) ($data['minimum'] ?? 0),
            'store_id'     => session('adminStoreId'),
        ];
        if (!empty($data['date_available'])) {
            $dataUpdate['date_available'] = $data['date_available'];
        }
        $product->update($dataUpdate);


        //Update custom field
        if (!empty($data['fields'])) {
            (new ShopCustomFieldDetail)
                ->join(lc_DB_PREFIX.'shop_custom_field', lc_DB_PREFIX.'shop_custom_field.id', lc_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->select('code', 'name', 'text')
                ->where(lc_DB_PREFIX.'shop_custom_field_detail.rel_id', $product->id)
                ->where(lc_DB_PREFIX.'shop_custom_field.type', 'product')
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
        $product->promotionPrice()->delete();
        if (isset($data['price_promotion']) && in_array($product['kind'], [lc_PRODUCT_SINGLE, lc_PRODUCT_BUILD])) {
            $arrPromotion['price_promotion'] = $data['price_promotion'];
            $arrPromotion['date_start'] = $data['price_promotion_start'] ? $data['price_promotion_start'] : null;
            $arrPromotion['date_end'] = $data['price_promotion_end'] ? $data['price_promotion_end'] : null;
            $product->promotionPrice()->create($arrPromotion);
        }

        $product->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'product_id' => $id,
                'lang' => $code,
                'name' => $row['name'],
                'keyword' => $row['keyword'],
                'description' => $row['description'],
                'content' => $row['content'] ?? '',
            ];
        }
        Product::insertDescriptionAdmin($dataDes);

        $product->categories()->detach();
        if (count($category)) {
            $product->categories()->attach($category);
        }

        //Update group
        if ($product['kind'] == lc_PRODUCT_GROUP) {
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
        if ($product['kind'] == lc_PRODUCT_BUILD) {
            $product->builds()->delete();
            if ($productBuild && $product['kind'] == lc_PRODUCT_BUILD) {
                $arrDataBuild = [];
                foreach ($productBuild as $key => $value) {
                    $arrDataBuild[] = new ShopProductBuild($value);
                }
                $product->builds()->saveMany($arrDataBuild);
            }
        }

        //Update path download
        (new ShopProductDownload)->where('product_id', $product->id)->delete();
        if ($product['property'] == lc_PROPERTY_DOWNLOAD && $downloadPath) {
            (new ShopProductDownload)->insert(['product_id' => $product->id, 'path' => $downloadPath]);
        }


        //Update attribute
        if ($product['kind'] == lc_PRODUCT_SINGLE) {
            $product->attributes()->delete();
            if (count($attribute)) {
                $arrDataAtt = [];
                foreach ($attribute as $group => $rowGroup) {
                    if (count($rowGroup)) {
                        foreach ($rowGroup['name'] as $key => $nameAtt) {
                            if ($nameAtt) {
                                $code = '';
                                $images = '';
                                if (array_key_exists('code', $rowGroup)) {
                                    $code = $rowGroup['code'][$key];
                                }
                                if (array_key_exists('images', $rowGroup)) {
                                    $images = $rowGroup['images'][$key];
                                }
                                if (array_key_exists('type_show', $rowGroup)) {
                                    $type_show = $rowGroup['type_show'][$key];
                                }
                                $arrDataAtt[] = new ShopProductAttribute([
                                                                        'name' => $nameAtt, 
                                                                        'add_price' => $rowGroup['add_price'][$key],
                                                                        'attribute_group_id' => $group,
                                                                        'code' => $code,
                                                                        'images'=>$images,
                                                                        'type_show' => $type_show
                                                                    ]);
                            }
                        }
                    }

                }
                $product->attributes()->saveMany($arrDataAtt);
            }

        }

        //Update sub mages
        if ($subImages && in_array($product['kind'], [lc_PRODUCT_SINGLE, lc_PRODUCT_BUILD])) {
            $product->img()->delete();
            $SubImages = new ShopProductImage(['image'=>$subImages,'type_show'=>$type_show_image_desc]);
            $product->img()->save($SubImages);
        }

        lc_clear_cache('cache_product');

        return redirect()->route('admin_product.index')->with('success', trans('product.admin.edit_success'));

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
    public function validateAttribute(array $arrValidation) {
        if (lc_config_admin('product_brand')) {
            if (lc_config_admin('product_brand_required')) {
                $arrValidation['brand_id'] = 'required|numeric';
            } else {
                $arrValidation['brand_id'] = 'nullable|numeric';
            }
        }

        if (lc_config_admin('product_supplier')) {
            if (lc_config_admin('product_supplier_required')) {
                $arrValidation['supplier_id'] = 'required';
            } else {
                $arrValidation['supplier_id'] = 'nullable';
            }
        }

        if (lc_config_admin('product_price')) {
            if (lc_config_admin('product_price_required')) {
                $arrValidation['price'] = 'required|numeric|min:0';
            } else {
                $arrValidation['price'] = 'nullable|numeric|min:0';
            }
        }

        if (lc_config_admin('product_cost')) {
            if (lc_config_admin('product_cost_required')) {
                $arrValidation['cost'] = 'required|numeric|min:0';
            } else {
                $arrValidation['cost'] = 'nullable|numeric|min:0';
            }
        }

        if (lc_config_admin('product_promotion')) {
            if (lc_config_admin('product_promotion_required')) {
                $arrValidation['price_promotion'] = 'required|numeric|min:0';
            } else {
                $arrValidation['price_promotion'] = 'nullable|numeric|min:0';
            }
        }

        if (lc_config_admin('product_stock')) {
            if (lc_config_admin('product_stock_required')) {
                $arrValidation['stock'] = 'required|numeric';
            } else {
                $arrValidation['stock'] = 'nullable|numeric';
            }
        }

        if (lc_config_admin('product_property')) {
            if (lc_config_admin('product_property_required')) {
                $arrValidation['property'] = 'required|string';
            } else {
                $arrValidation['property'] = 'nullable|string';
            }
        }

        if (lc_config_admin('product_available')) {
            if (lc_config_admin('product_available_required')) {
                $arrValidation['date_available'] = 'required|date';
            } else {
                $arrValidation['date_available'] = 'nullable|date';
            }
        }

        if (lc_config_admin('product_weight')) {
            if (lc_config_admin('product_weight_required')) {
                $arrValidation['weight'] = 'required|numeric';
                $arrValidation['weight_class'] = 'required|string';
            } else {
                $arrValidation['weight'] = 'nullable|numeric';
                $arrValidation['weight_class'] = 'nullable|string';
            }
        }

        if (lc_config_admin('product_length')) {
            if (lc_config_admin('product_length_required')) {
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
}

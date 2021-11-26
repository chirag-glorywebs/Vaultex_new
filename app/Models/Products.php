<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\ProductDetail;
use App\Models\ProductAttribute;
use App\Models\ProductVariantCombination;
use App\Models\ProductCategory;

class Products extends Model
{
    use HasFactory;
    
    protected $fillable = ['sku', 'short_description', 'regular_price', 'in_stock', 'brand_id'];
   // protected $fillable = ['product_name', 'description', 'main_image', 'sku', 'short_description', 'regular_price', 'in_stock', 'brand_id', 'status', 'atributes'];

    public function productBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id')->select(array('brand_name', 'slug'));
    }

    public function productCategory()
    {
        return $this->belongsTo(Categories::class, 'category_id')->select(array('category_name', 'slug'));
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }

    public function productCategories()
    {
        // return $this->hasMany(ProductCategory::class, 'product_id', 'id');
        return $this->hasMany(ProductCategory::class,'product_id','id')
        ->leftjoin('categories', 'categories.id','=','category_id');
    }    

    public function productDetails()
    {
        return $this->hasOne(ProductDetail::class, 'product_id', 'id');
    }

    public function productVariantCombinations()
    {
        return $this->hasMany(ProductVariantCombination::class, 'product_id', 'id');

    }

    /* **********************************************************
    ** Start - Remove product related data 
    ********************************************************** */
    
    // Declare event handlers
    public static function boot() {
        parent::boot();
        // Before delete Product, Remove all related data
        static::deleting(function($product) { 
            $product->productDetails()->delete();
            $product->productAttributes()->delete();
            $product->productVariantCombinations()->delete();
        });
    }
    /* **********************************************************
    ** End - Remove product related data
    ********************************************************** */
    public static function getProductPrice($userId, $productId) {
        $productsData =  Products::WITH('productCategories')
                    ->join('price_lists', 'products.sku', '=', 'price_lists.item_no') 
                    ->join('users', 'price_lists.price_list_no', '=', 'users.price_list_no')
                    ->select(
                        'products.id',
                        'price_lists.list_price AS uprice', 
                        'products.regular_price', 
                        'products.sale_price',
                        'sku')
                    ->where('users.id', $userId)
                    ->where('products.id', $productId)
                    ->first();
        return $productsData;
    }


    public function filterProducts()
    {
        # code...
        $Brand = Brand::select('id', 'brand_name AS name')->where('status', 1)->get();
        $Brand_data = $Brand->take(10);
        foreach ($Brand as $data) {
            $bid = $data->id;
            $data['count'] = Products::join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('brands.id', $bid)->count('products.brand_id');
        }

        $category = Categories::whereNull('parent_category')->select('id', 'category_name AS name', 'slug')
            ->where('status', 1)->get();

        $price = array();
        array_push($price, array("name" => "0 - 100", "value" => "0,100"));
        array_push($price, array("name" => "101 - 500", "value" => "101,500"));
        array_push($price, array("name" => "501 - 1000", "value" => "501,1000"));
        array_push($price, array("name" => "1001 - 5000", "value" => "1001,5000"));
        array_push($price, array("name" => "5001 - 10000", "value" => "5001,10000"));
        array_push($price, array("name" => "10001 and above", "value" => "10001,-1"));
        $discount = array();
        array_push($discount, array("name" => "0% - 10%", "value" => "0,10"));
        array_push($discount, array("name" => "11% - 20%", "value" => "11,20"));
        array_push($discount, array("name" => "21% - 30%", "value" => "21,30"));
        array_push($discount, array("name" => "31% - 40%", "value" => "31,40"));
        array_push($discount, array("name" => "41% - 50%", "value" => "41,50"));
        array_push($discount, array("name" => "51% and above", "value" => "51,100"));

        $Badges = ['Best Seller', 'Best Deal', 'Fast Delivery'];
        $avaliablity = ['Show in stock only'];
        //  $responseData = array('Popular_Brands'=>$Brand,'Category'=>$category,'Price_in_AED'=>$price);
        $responseData = array(
            'Popular_Brands' => $Brand_data,
            'Category' => $category, 'Price_in_AED' => $price, 'Brands' => $Brand,
            'Discount' => $discount, 'Badges' => $Badges, 'Availability' => $avaliablity
        );
        return $responseData;
    }

    public $Attributes;

    public $import
    = [
        'fields' => [
            'sku' => [
                'displayName' => 'sku',
            ],
            'product_name' => [
                'displayName' => 'product_name',
            ],
            'description' => [
                'displayName' => 'description',
            ],
            'short_description' => [
                'displayName' => 'short_description',
            ],
            'features' => [
                'displayName' => 'features',
            ],
            'product_icons' => [
                'displayName' => 'product_icons',
            ],
            'category_id' => [
                'displayName' => 'category_id',
            ],
            'brand_id' => [
                'displayName' => 'brand_id',
            ],           
            'main_image' => [
                'displayName' => 'main_image',
            ],
            'regular_price' => [
                'displayName' => 'regular_price',
            ],
            'sale_price' => [
                'displayName' => 'sale_price',
            ],
            'inventory' => [
                'displayName' => 'Inventory',
            ],
            'IsCommited' => [
                'displayName' => 'IsCommited',
            ],
            'OnOrder' => [
                'displayName' => 'OnOrder',
            ],
            'specification' => [
                'displayName' => 'specification',
            ],
            'tech_documents' => [
                'displayName' => 'tech_documents',
            ],
            'video' => [
                'displayName' => 'video',
            ],
            'gallery' => [
                'displayName' => 'gallery',
            ],
            'download_datasheet' => [
                'displayName' => 'download_datasheet',
            ],
            'Attributes' => [
                'displayName' => 'Attributes',
            ],
            'packaging_delivery_descr' => [
                'displayName' => 'packaging_delivery_descr',
            ],
            'packaging_delivery_images' => [
                'displayName' => 'packaging_delivery_images',
            ],
            'trending_product' => [
                'displayName' => 'trending_product',
            ],
            'best_selling' => [
                'displayName' => 'best_selling',
            ],
            'bid_quote' => [
                'displayName' => 'bid_quote',
            ],
            'seo_title' => [
                'displayName' => 'seo_title',
            ],
            'seo_description' => [
                'displayName' => 'seo_description',
            ],
            'seo_keyword' => [
                'displayName' => 'seo_keyword',
            ],
            'status' => [
                'displayName' => 'status',
            ],
            'VatGourpSa' => [
                'displayName' => 'VatGourpSa',
            ],
            'VatGroupPu' => [
                'displayName' => 'VatGroupPu',
            ],
            'U_Size' => [
                'displayName' => 'U_Size',
            ],
            'SizeName' => [
                'displayName' => 'SizeName',
            ],
            'U_SCartQty' => [
                'displayName' => 'U_SCartQty',
            ],
            'U_CBM' => [
                'displayName' => 'U_CBM',
            ],
            'OnHand' => [
                'displayName' => 'OnHand',
            ],
            'U_Itemgrp' => [
                'displayName' => 'U_Itemgrp',
            ],
            'U_Itemgrpname' => [
                'displayName' => 'U_Itemgrpname',
            ],
            'U_OrgCountCod' => [
                'displayName' => 'U_OrgCountCod',
            ],
            'U_OrgCountNam' => [
                'displayName' => 'U_OrgCountNam',
            ],
            'U_CartQty' => [
                'displayName' => 'U_CartQty',
            ],
            'SuppCatNum' => [
                'displayName' => 'SuppCatNum',
            ],
            'BuyUnitMsr' => [
                'displayName' => 'BuyUnitMsr',
            ],
            'SalUnitMsr' => [
                'displayName' => 'SalUnitMsr',
            ],
            'FirmCode' => [
                'displayName' => 'FirmCode',
            ],
            'FirmName' => [
                'displayName' => 'FirmName',
            ],
            'U_HsCode' => [
                'displayName' => 'U_HsCode',
            ],
            'U_HsName' => [
                'displayName' => 'U_HsName',
            ],
            'QryGroup1' => [
                'displayName' => 'QryGroup1',
            ],
            'QryGroup2' => [
                'displayName' => 'QryGroup2',
            ],
            'QryGroup3' => [
                'displayName' => 'QryGroup3',
            ],
            'QryGroup4' => [
                'displayName' => 'QryGroup4',
            ],
            'QryGroup5' => [
                'displayName' => 'QryGroup5',
            ],
            'QryGroup6' => [
                'displayName' => 'QryGroup6',
            ],
            'QryGroup7' => [
                'displayName' => 'QryGroup7',
            ],
            'QryGroup8' => [
                'displayName' => 'QryGroup8',
            ],
            'QryGroup9' => [
                'displayName' => 'QryGroup9',
            ],
            'QryGroup10' => [
                'displayName' => 'QryGroup10',
            ],
            'QryGroup11' => [
                'displayName' => 'QryGroup11',
            ],
            'QryGroup12' => [
                'displayName' => 'QryGroup12',
            ],
            'QryGroup13' => [
                'displayName' => 'QryGroup13',
            ],
            'QryGroup14' => [
                'displayName' => 'QryGroup14',
            ],
            'QryGroup15' => [
                'displayName' => 'QryGroup15',
            ],
            'QryGroup16' => [
                'displayName' => 'QryGroup16',
            ],
            'QryGroup17' => [
                'displayName' => 'QryGroup17',
            ],
            'QryGroup18' => [
                'displayName' => 'QryGroup18',
            ],
            'QryGroup19' => [
                'displayName' => 'QryGroup19',
            ],
            'QryGroup20' => [
                'displayName' => 'QryGroup20',
            ],
            'QryGroup21' => [
                'displayName' => 'QryGroup21',
            ],
            'QryGroup22' => [
                'displayName' => 'QryGroup22',
            ],
            'QryGroup23' => [
                'displayName' => 'QryGroup23',
            ],
            'QryGroup24' => [
                'displayName' => 'QryGroup24',
            ],
            'QryGroup25' => [
                'displayName' => 'QryGroup25',
            ],
            'QryGroup26' => [
                'displayName' => 'QryGroup26',
            ],
            'QryGroup27' => [
                'displayName' => 'QryGroup27',
            ],
            'QryGroup28' => [
                'displayName' => 'QryGroup28',
            ],
            'QryGroup29' => [
                'displayName' => 'QryGroup29',
            ],
            'QryGroup30' => [
                'displayName' => 'QryGroup30',
            ],
            'QryGroup31' => [
                'displayName' => 'QryGroup31',
            ],
            'QryGroup32' => [
                'displayName' => 'QryGroup32',
            ],
            'QryGroup33' => [
                'displayName' => 'QryGroup33',
            ],
            'QryGroup34' => [
                'displayName' => 'QryGroup34',
            ],
            'QryGroup35' => [
                'displayName' => 'QryGroup35',
            ],
            'QryGroup36' => [
                'displayName' => 'QryGroup36',
            ],
            'QryGroup37' => [
                'displayName' => 'QryGroup37',
            ],
            'QryGroup38' => [
                'displayName' => 'QryGroup38',
            ],
            'QryGroup39' => [
                'displayName' => 'QryGroup39',
            ],
            'QryGroup40' => [
                'displayName' => 'QryGroup40',
            ],
            'QryGroup41' => [
                'displayName' => 'QryGroup41',
            ],
            'QryGroup42' => [
                'displayName' => 'QryGroup42',
            ],
            'QryGroup43' => [
                'displayName' => 'QryGroup43',
            ],
            'QryGroup44' => [
                'displayName' => 'QryGroup44',
            ],
            'QryGroup45' => [
                'displayName' => 'QryGroup45',
            ],
            'QryGroup46' => [
                'displayName' => 'QryGroup46',
            ],
            'QryGroup47' => [
                'displayName' => 'QryGroup47',
            ],
            'QryGroup48' => [
                'displayName' => 'QryGroup48',
            ],
            'QryGroup49' => [
                'displayName' => 'QryGroup49',
            ],
            'QryGroup50' => [
                'displayName' => 'QryGroup50',
            ],
            'QryGroup51' => [
                'displayName' => 'QryGroup51',
            ],
            'QryGroup52' => [
                'displayName' => 'QryGroup52',
            ],
            'QryGroup53' => [
                'displayName' => 'QryGroup53',
            ],
            'QryGroup54' => [
                'displayName' => 'QryGroup54',
            ],
            'QryGroup55' => [
                'displayName' => 'QryGroup55',
            ],
            'QryGroup56' => [
                'displayName' => 'QryGroup56',
            ],
            'QryGroup57' => [
                'displayName' => 'QryGroup57',
            ],
            'QryGroup58' => [
                'displayName' => 'QryGroup58',
            ],
            'QryGroup59' => [
                'displayName' => 'QryGroup59',
            ],
            'QryGroup60' => [
                'displayName' => 'QryGroup60',
            ],
            'QryGroup61' => [
                'displayName' => 'QryGroup61',
            ],
            'QryGroup62' => [
                'displayName' => 'QryGroup62',
            ],
            'QryGroup63' => [
                'displayName' => 'QryGroup63',
            ],
            'QryGroup64' => [
                'displayName' => 'QryGroup64',
            ],
        ],
    ];

}
<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LikedProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController;
use App\Models\Products;

class WishlistController extends BaseController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
            $liked_product = new LikedProduct;
            $user = Auth::user();

            $liked_product->liked_products_id = $request->product_id;
            $liked_product->liked_customers_id = $user->id;
            $liked_product->date_liked = date('Y-m-d');
            $data = $request->product_id;

            $product_count = LikedProduct::where(['liked_products_id' => $data,'liked_customers_id' => Auth::user()->id])->count();

            if($product_count > 0) {
                return $this->sendError('Product is allready Added into Wishlist');
                    
                } else {
                    $data = $liked_product->save();
                    return $this->sendResponse($data,'Your Product is successfully added into wishlist');
                }

            }

    }

    public function index(Request $request)
    {
        # code...
        $userID =  $request->user()->id;
        $orderby = (isset($request->orderby)) ?   $request->orderby : "products.id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
        $perPage = (isset($request->per_page)) ?  intval($request->per_page) : 10;

        /* $query = Products::join('liked_products', 'products.id', '=', 'liked_products.liked_products_id')
            ->where('liked_customers_id', $userID)
            ->select([
                'liked_products.id', 'products.id', 'products.product_name', 'products.regular_price', 'products.sale_price', 'products.category_id',
                'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification', 'products.tech_documents', 'products.video'
            ])
            ->orderby($orderby, $order); */
            
                $user_id = $request->user('api')->id;
                $query = DB::table('products')
                    ->join('price_lists', 'products.sku', '=', 'price_lists.item_no') 
                    ->join('users', 'price_lists.price_list_no', '=', 'users.price_list_no')
                    ->join('liked_products', 'products.id', '=', 'liked_products.liked_products_id')
                    ->select(
                        'products.id',
                        'products.product_name',
                        'products.product_type',
                        'products.regular_price',
                        'products.sale_price',
                        // 'products.category_id',
                        'products.main_image',
                        'products.medium_image',
                        'products.sku',
                        'products.slug',
                        'products.short_description',
                        'products.specification',
                        'products.trending_product',
                        'products.best_selling',
                        'price_lists.list_price AS uprice', 
                        DB::raw('COALESCE(price_lists.list_price, products.regular_price) as price'), 
                        DB::raw('COALESCE(CAST(((products.regular_price - price_lists.list_price) * 100 / products.regular_price) as decimal(5,2)),0) discount'))
                     
                     ->where('users.id', '=', $user_id)
                     ->where('liked_customers_id', $userID)
                     ->where('products.status', 1)
                     ->orderby($orderby, $order);
             
        // ->get();
        if ($perPage > 0) {
            $query = $query->paginate($perPage);
        }
        // return $query;
        foreach ($query as $item) {
            if (!empty($item->main_image) && file_exists($item->main_image)) {
                $item->main_image = asset($item->main_image);
            } else {
                $item->main_image = asset('uploads/placeholder-medium.jpg');
            }

            if(!empty($item->medium_image) && file_exists($item->medium_image)){
                $item->medium_image = asset($item->medium_image);
            }else{
                $item->medium_image = asset('uploads/placeholder-medium.jpg');
            }

            if(!empty($item->thumbnail_image) && file_exists($item->thumbnail_image)){
                $item->thumbnail_image = asset($item->thumbnail_image);
            }else{
                $item->thumbnail_image = asset('uploads/placeholder-medium.jpg');
            }


            if (!empty($item->video) && file_exists($item->video)) {
                $item->video = asset($item->video);
            }
            $item->wishlist = true;
        }
        if (!$query->isEmpty()) {
            return $this->sendResponse($query, 'Wishlist Products');
        } else {
            return $this->sendError('404 data not found.', $query);
        }
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);
        $pid =  $request->product_id;
        $userID =  $request->user()->id;

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {

            $data =  LikedProduct::where('liked_products_id', $pid)->where('liked_customers_id', $userID)->first();
            if (!$data == null) {
                $data = $data->delete();
                return $this->sendResponse($data, 'Product has been successfully removed form wishlist.');
            } else {
                return $this->sendError('404 data not found.', $data);
            }
        }
     }
}

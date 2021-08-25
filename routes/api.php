<?php

use App\Http\Controllers\API\BlogCategoryController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\BlogReviewController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\BulkOrderController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\ContactusController;
use App\Http\Controllers\API\FacebookController;
use App\Http\Controllers\API\GoogleController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\ImportDataController;
use App\Http\Controllers\API\InvoiceDetailsController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PageController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\ProductReviewController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\API\VendorEmailController;
use App\Http\Controllers\API\UserProductVideoController;


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/* Mobile APP */

Route::get('/home', [HomeController::class, 'home'])->name('api.blogs');
Route::get('homeweb',[HomeController::class,'homePage'])->name('api.homeweb');
// without resource
Route::post('/register', [UserController::class, 'register'])->name('api.register');
Route::post('/login', [UserController::class, 'login'])->name('api.login');
Route::post('/googleLogin', [GoogleController::class, 'googleLogin'])->name('api.googleLogin');
Route::post('/facebookLogin', [FacebookController::class, 'facebookLogin'])->name('api.facebookLogin');

Route::post('/verifyVendorInfo', [VendorEmailController::class, 'verifyVendor'])->name('api.verifyVendorInfo');
Route::post('/submitVendorInfo', [VendorEmailController::class, 'store'])->name('api.submitVendorInfo');

/* Forgot and Reset Password Routes */
Route::post('/lostPassword', [PasswordResetController::class, 'forgot'])->name('api.forgot');
Route::post('/lostPassword/verifyOTP', [PasswordResetController::class, 'find'])->name('api.find');
Route::post('/resetPassword', [PasswordResetController::class, 'reset'])->name('api.reset');
Route::post('/resetPasswordweb', [PasswordResetController::class, 'resetPassword'])->name('api.resetPassword');
Route::post('/resetPasswordChekTokenWeb', [PasswordResetController::class, 'resetPasswordCheckToken'])->name('api.resetPasswordVerifyToken');

/* Settings */
Route::get('settings', [SettingController::class, 'get'])->name('api.get');

/* CONTACT US */
Route::post('/conatctus', [ContactusController::class, 'create'])->name('api.conatctuscreate');

/* Blog Routes */
Route::get('/blog', [BlogController::class, 'getBlogList'])->name('api.getBlogList');
Route::get('/blog/{id}', [BlogController::class, 'getBlogDetailsByID'])->name('api.getBlogDetailsByID');
Route::post('/blogreview/create', [BlogReviewController::class, 'createReviews'])->name('api.createReviews');
Route::get('/blogreview', [BlogReviewController::class, 'showReviews'])->name('api.showReviews');
Route::get('/blogreview/{blog_review_id}', [BlogReviewController::class, 'reviews'])->name('api.reviews');

Route::get('/blogCategory', [BlogCategoryController::class, 'index'])->name('api.getBlogCategories');

/* Product Routes */
Route::get('/productCategory', [CategoriesController::class, 'index'])->name('api.allCategory');
Route::get('/products', [ProductsController::class, 'peoductList'])->name('api.peoductList');
Route::get('products/porductVariant', [ProductsController::class, 'getPorductVariant'])->name('api.product.getPorductVariant');


Route::get('/products/{id}', [ProductsController::class, 'ProductInfo'])->name('api.ProductInfo');
Route::get('productweb/{product_slug}', [ProductsController::class, 'productDetails'])->name('api.productDetails');
Route::get('/productsByIds', [ProductsController::class, 'getProductsByIds'])->name('api.product.byids');
Route::get('/productSearchBySKU', [ProductsController::class, 'getPorductDetailsBySKU'])->name('api.product.serchbysku');
Route::get('/getAllProductsSKU', [ProductsController::class, 'getAllProductsSKU'])->name('api.product.getAllProductsSKU');
Route::post('/createProductVideoRequest', [UserProductVideoController::class, 'store'])->name('api.product.videoRequest');
Route::get('/getProductVideos', [UserProductVideoController::class, 'index'])->name('api.product.videos');
Route::get('/deleteProductVideos', [UserProductVideoController::class, 'destroy'])->name('api.product.deleteVideo');

# search product api
Route::get('/product/search',[ProductsController::class,'searchProducts'])->name('api.product.serach');
#filter Products
Route::get('filterList',[ProductsController::class,'filterList'])->name('api.filterlist');
Route::get('filterProducts',[ProductsController::class,'filterProducts'])->name('api.filter.products');

/* Route::get('/productListBycat', [ProductsController::class, 'productListBycat'])->name('api.productListBycat');
Route::get('/productListByBrand', [ProductsController::class, 'productListByBrand'])->name('api.productListByBrand');
 */

/* Product Review */
Route::post('/productreview/create', [ProductReviewController::class, 'create'])->name('api.create');
Route::get('/productreview/{productreview_id}', [ProductReviewController::class, 'reviews'])->name('api.reviews');
Route::get('/productreview', [ProductReviewController::class, 'showReviews'])->name('api.showReviews');

/* Brand Route*/
Route::get('/brands', [BrandController::class, 'index'])->name('api.brandList');

//Pages [ABOUT_US]
Route::get('pages/{slug}', [PageController::class, 'show'])->name('api.pageShow');

Route::get('getInvoice', [InvoiceDetailsController::class, 'getInvoice'])->name('api.getInvoice');

Route::group(['middleware' => 'auth:api'], function () {
	Route::post('logout', [UserController::class, 'logout'])->name('api.logout');
});

/* For Website */

/* 
Route::get('/product/{product_slug}', [ProductsController::class, 'productDetails'])->name('api.productDetails');
// Route::get('pages/{slug}', [PageController::class, 'detailsPage'])->name('api.pageSlug');
Route::get('page/{slug}',[PageController::class,'pageDetails']); */
Route::get('productweb/{product_slug}', [ProductsController::class, 'productDetails'])->name('api.productDetails');
Route::get('/blogweb/{blog_slug}', [BlogController::class, 'getBlogDetails'])->name('api.getBlogDetails');
Route::get('productCategory/{category_slug}',[CategoriesController::class,'productCategoryList']);
/* Import data from Thid party APIs */
Route::get('/importVendor', [ImportDataController::class, 'vendorImport'])->name('api.vendorImport');
Route::get('/importProduct', [ImportDataController::class, 'productImport'])->name('api.productImport');
Route::get('/importProductPrice', [ImportDataController::class, 'productPriceImport'])->name('api.productPriceImport');
Route::get('/GetInvoiceDetails', [ImportDataController::class, 'GetInvoiceDetails'])->name('api.GetInvoiceDetails');

/* CONTACT US */
Route::post('/conatctus', [ContactusController::class, 'create'])->name('api.conatctuscreate');

Route::group(['middleware' => 'auth:api'], function () {

	/* CART ROUTES */
	Route::post('/carts/add', [CartController::class, 'store'])->name('api.cart.store');
	Route::get('/carts', [CartController::class, 'index'])->name('api.cart.index');
	Route::post('/carts/destroy', [CartController::class, 'destroy'])->name('api.cart.destroy');
	Route::post('/carts/update', [CartController::class, 'update'])->name('api.cart.update');
	Route::post('/applyCoupon', [CartController::class, 'applyCoupon'])->name('api.cart.applyCoupon');

	/* BULK ORDER ROUTES */
	Route::post('/rfq/create', [BulkOrderController::class, 'create'])->name('api.rfq.create');
	Route::get('/rfq', [BulkOrderController::class, 'index'])->name('api.rfq.index');
	Route::post('/rfq/createbyproductsku', [BulkOrderController::class, 'createByProductPage'])->name('api.rfq.createByProductPage');
	Route::post('/rfq/{id}', [BulkOrderController::class, 'showBulkDetails'])->name('api.rfq.details');

	
	
	Route::post('logout', [UserController::class, 'logout']);
	Route::post('/changepassword', [PasswordResetController::class, 'changePassword'])->name('api.changePassword');
	
	/* WishList Routes */
	Route::post('/addToWishlist', [WishlistController::class, 'store'])->name('api.wishlist.store');
	Route::get('/wishlist', [WishlistController::class, 'index'])->name('api.wishlist.index');
	Route::post('/RemoveFromwishlist', [WishlistController::class, 'destroy'])->name('api.wishlist.destroy');

	/*Update profile */
	Route::get('/user', [UserController::class, 'user'])->name('api.user');
	Route::post('updateProfile', [UserController::class, 'updateProfile'])->name('api.updateProfile');
	Route::get('/userProfile', [UserController::class, 'showProfile'])->name('api.showProfile');
	Route::post('/setDefaultAddress', [UserController::class, 'setDefaultAddress'])->name('api.setDefaultAddress');
	
	Route::get('/checkout', [OrderController::class, 'getInfo'])->name('api.checkout.getInfo');
	Route::post('/placeOrder', [OrderController::class, 'placeOrder'])->name('api.checkout.placeOrder');

	/* Add and Update Address API */
	Route::post('/addresses/add', [UserController::class, 'add'])->name('api.addresses.add');
	Route::post('/addresses/update', [UserController::class, 'updateAdrdess'])->name('api.addresses.update');
	Route::post('/addresses/delete', [UserController::class, 'deleteAddress'])->name('api.addresses.delete');
	Route::get('/addresses', [UserController::class, 'showAddress'])->name('api.addresses.show');

// Route::get('/user',[UserController::class,'user'])->name('api.user');
  /* MY ORDER */
  Route::get('/orders',[OrderController::class,'orderList'])->name('api.orderList');
  Route::get('/productsByOrders',[OrderController::class,'orderItemList'])->name('api.orderItemList');
  Route::get('/orders/{id}',[OrderController::class,'viewOrder'])->name('api.viewOrder');

});

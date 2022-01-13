<?php

use App\Helpers\Helper;
use App\Http\Controllers\BulkOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\GeneralSettingsControllers;
use App\Http\Controllers\ProductReviewController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Route::get('/', function () {
    return view('welcome');
});*/

/*Route::get('/', 'AdminUserController@index');
Route::get('/admin', 'AdminUserController@index');
Route::group(['prefix' => 'admin'], function () {
    Route::post('/checklogin', 'AdminUserController@adminLogin');
});
*/

Route::get('order/{order_id}', function($order_id) {
    Helper::salesOrderApi($order_id);
});

Route::group(['middleware' => ['guest','prevent-back-history','check-admin']], function () {
    Route::get('/', 'AdminUserController@index');
    Route::get('/admin', 'AdminUserController@index');
    Route::post('/checklogin',[AdminUserController::class,'adminLogin']);
});
Route::group(['prefix' => 'admin', 'middleware' => ['auth','prevent-back-history','check-admin']], function () {
    Route::get('/checklogout', 'AdminUserController@adminLogout');

    Route::group(['middleware' => ['check-menu-access']], function () {
        Route::get('/dashboard', 'AdminUserController@dashboard')->name('dashboard');
        /* set the group route for admin user*/
        Route::get('/adminuser', 'AdminUserController@get')->name('admin.adminuser');
        Route::prefix('/adminuser')->group(function () {
            Route::get('/add', 'AdminUserController@add');
            Route::post('/create', 'AdminUserController@create');
            Route::delete('/destroy/{id}', 'AdminUserController@destroy');
            Route::get('/edit/{id}', 'AdminUserController@edit');
            Route::post('/update', 'AdminUserController@update');
        });
        /* set the group route for sales user*/
        Route::get('/salesuser', 'SalesUserController@get')->name('admin.salesuser');
        Route::prefix('/salesuser')->group(function () {
            Route::get('/add', 'SalesUserController@add');
            Route::post('/create', 'SalesUserController@create');
            Route::delete('/delete/{id}', 'SalesUserController@delete');
            Route::get('/edit/{id}', 'SalesUserController@edit');
            Route::post('/update', 'SalesUserController@update');
        });
        /* set the group route for vendor user*/
        Route::get('/vendoruser', 'VendorUserController@get')->name('vendoruser.index');
        Route::prefix('/vendoruser')->group(function () {
            Route::get('/add', 'VendorUserController@add');
            Route::post('/create', 'VendorUserController@create');
            Route::delete('/delete/{id}', 'VendorUserController@delete');
            Route::get('/edit/{id}', 'VendorUserController@edit');
            Route::post('/update', 'VendorUserController@update');
        });
        /* set the group route for customer user*/
        Route::get('/view/{id}', 'VendorUserController@view');
        Route::get('/customers', 'CustomersController@index')->name('customers.index');
        Route::prefix('/customers')->group(function () {
            Route::delete('/delete/{id}', 'CustomersController@delete');
            Route::get('/edit/{id}', 'CustomersController@edit');
            Route::post('/update', 'CustomersController@update');
        });

        /* here start all product catelog like brand, category, attributes, products*/
        /* set the brand route */
        Route::get('/brand', 'BrandController@get')->name('brand.index');
        Route::prefix('/brand')->group(function () {
            Route::get('/add', 'BrandController@add');
            Route::post('/create', 'BrandController@create');
            Route::delete('/delete/{id}', 'BrandController@delete');
            Route::get('/edit/{id}', 'BrandController@edit');
            Route::post('/update', 'BrandController@update');
        });
        /* set the category route */
        Route::get('/category', 'CategoriesController@get')->name('category.index');
        Route::prefix('/category')->group(function () {
            Route::get('/add', 'CategoriesController@add');
            Route::post('/create', 'CategoriesController@create');
            Route::delete('/delete/{id}', 'CategoriesController@delete');
            Route::get('/edit/{id}', 'CategoriesController@edit');
            Route::post('/update', 'CategoriesController@update');
        });
        /* set the attributes route */
        Route::get('/attributes', 'AttributesController@get')->name('attributes.index');
        Route::prefix('/attributes')->group(function () {
            Route::get('/add', 'AttributesController@add');
            Route::post('/create', 'AttributesController@create');
            Route::delete('/delete/{id}', 'AttributesController@delete');
            Route::get('/edit/{id}', 'AttributesController@edit');
            Route::post('/update', 'AttributesController@update');

            Route::get('{attid}/variations', 'AttributesController@getVariation')->name('variations.index');
            Route::prefix('{attid}/variations')->group(function () {
                Route::get('/add', 'AttributesController@addVariation');
                Route::post('/create', 'AttributesController@createVariation');
                Route::delete('/delete/{id}', 'AttributesController@deleteVariation');
                Route::get('/edit/{id}', 'AttributesController@editVariation');
                Route::post('/update', 'AttributesController@updateVariation');
            });
        });
        /* set the attributes route */
        Route::get('/products', 'ProductsController@get')->name('products.index');
        Route::prefix('/products')->group(function () {
            Route::get('/add', 'ProductsController@add');
            Route::post('/create', 'ProductsController@create');
            Route::delete('/delete/{id}', 'ProductsController@delete');

            // Multiple products delete
            Route::post('delete-multi-products', 'ProductsController@deleteAll');            

            Route::get('/edit/{id}', 'ProductsController@edit');
            Route::post('/update', 'ProductsController@update');
            Route::post('/updateGallery', 'ProductsController@updateGallery');
            Route::post('/updateAttribute', 'ProductsController@updateAttribute');
            Route::post('/updateTechDoc', 'ProductsController@updateTechDoc');
            Route::post('/updateThreeSixty', 'ProductsController@updateThreeSixty');
            /* faq routes */
            Route::post('/faq/create', 'ProductsController@createFaqs');
            Route::post('/faq/update', 'ProductsController@updateFaqs');
            Route::get('/edit/{id}/faq/edit/{fid}', 'ProductsController@editFaqs');
            Route::get('/edit/{id}/faq/add', 'ProductsController@addFaqs');
            Route::delete('/edit/{id}/faq/delete/{fid}', 'ProductsController@deleteFaq');
            /* training videos routes */
            Route::post('/trainingvideo/create', 'ProductsController@createTrainingVideo');
            Route::post('/trainingvideo/update', 'ProductsController@updateTrainingVideo');
            Route::get('/edit/{id}/trainingvideo/edit/{tvid}', 'ProductsController@editTrainingVideo');
            Route::get('/edit/{id}/trainingvideo/add', 'ProductsController@addTrainingVideo');
            Route::delete('/edit/{id}/trainingvideo/delete/{tvid}', 'ProductsController@deleteTrainingVideo');
            /* features videos routes */
            Route::post('/featurevideo/create', 'ProductsController@createFeatureVideo');
            Route::post('/featurevideo/update', 'ProductsController@updateFeatureVideo');
            Route::get('/edit/{id}/featurevideo/edit/{tvid}', 'ProductsController@editFeatureVideo');
            Route::get('/edit/{id}/featurevideo/add', 'ProductsController@addFeatureVideo');
            Route::delete('/edit/{id}/featurevideo/delete/{tvid}', 'ProductsController@deleteFeatureVide');
            /* Add product Variant */
            Route::post('/addProductVariant', 'ProductVariantController@create');
            Route::post('/updateProductVariant', 'ProductVariantController@update');
            Route::delete('/deleteProductVariant/{id}', 'ProductVariantController@destroy');
            
        });
        /* set the orders route */
        Route::get('/orders', 'OrderController@get')->name('order.index');
        Route::prefix('/orders')->group(function () {
            Route::delete('/delete/{id}', 'OrderController@delete');
            Route::get('/edit/{id}', 'OrderController@edit');
            Route::post('/update', 'OrderController@update');
        });
        Route::get('/orderstatus', 'OrderController@getStatus')->name('orderstatus.index');
        Route::prefix('/orderstatus')->group(function () {
            Route::get('/add', 'OrderController@addStatus');
            Route::post('/create', 'OrderController@createStatus');
            Route::delete('/delete/{id}', 'OrderController@deleteStatus');
            Route::get('/edit/{id}', 'OrderController@editStatus');
            Route::post('/update', 'OrderController@updateStatus');
        });

        Route::group(['prefix' => '/coupons', 'middleware' => 'auth'], function () {
            Route::get('/', 'CouponController@index')->name('coupons.index');
            Route::get('/add', 'CouponController@create');
            Route::post('/', 'CouponController@store');
            Route::get('/edit/{id}', 'CouponController@edit');
            Route::post('/update', 'CouponController@update');
            Route::delete('/destroy/{id}', 'CouponController@destroy');
        });
        Route::group(['prefix' => '/blogcategories', 'middleware' => 'auth'], function () {
            Route::get('/', 'BlogCategoryController@index')->name('blogcategories.index');
            Route::get('/add', 'BlogCategoryController@create');
            Route::post('/', 'BlogCategoryController@store');
            Route::get('/edit/{id}', 'BlogCategoryController@edit');
            Route::post('/update', 'BlogCategoryController@update');
            Route::delete('/destroy/{id}', 'BlogCategoryController@destroy');
        });

        Route::group(['prefix' => '/blogs', 'middleware' => 'auth'], function () {
            Route::get('/', 'BlogController@index')->name('blogs.index');
            Route::get('/add', 'BlogController@create');
            Route::post('/', 'BlogController@store');
            Route::get('/edit/{id}', 'BlogController@edit');
            Route::post('/update', 'BlogController@update');
            Route::delete('/destroy/{id}', 'BlogController@destroy');
        });

        Route::group(['prefix' => '/pages', 'middleware' => 'auth'], function () {
            Route::get('/', 'PageController@index')->name('pages.index');
            Route::get('/add', 'PageController@create');
            Route::post('/', 'PageController@store');
            Route::get('/edit/{id}', 'PageController@edit');
            Route::post('/update', 'PageController@update');
            Route::delete('/destroy/{id}', 'PageController@destroy');
        });
        Route::get('/bulk-order', 'BulkOrderController@index')->name('bulk-order');
        Route::group(['prefix' => '/bulk-order', 'middleware' => 'auth'], function () {
            Route::post('/store-bulk-order', 'BulkOrderController@storeOrdersBulkData');
            Route::get('/edit/{id}', 'BulkOrderController@edit')->name('edit-bulk-order');
            Route::get('/show/{id}', 'BulkOrderController@show')->name('show-bulk-order');
            Route::post('/update', 'BulkOrderController@updateBulkOrder')->name('update-bulk-order');
            Route::delete('/delete/{id}', 'BulkOrderController@delete');
        
        });
        //Import Functonality
        Route::get('/import', 'ImportController@index')->name('import');
        Route::group(['prefix' => '/import', 'middleware' => 'auth'], function () {
            Route::post('/products-import', 'ImportController@importCsv')->name('products-import');
            Route::post('/faq-import', 'ImportController@faqImportCsv')->name('faq-import');
            Route::post('/training-video-import', 'ImportController@trainingVideoImportCsv')->name('training-video-import');
            Route::post('/feature-video-import', 'ImportController@featureVideoImportCsv')->name('feature-video-import');

        });

        Route::get('/settings', 'GeneralSettingsControllers@index')->name('settings');
        Route::group(['prefix' => '/settings', 'middleware' => 'auth'], function () {
            Route::post('/image-setting',[GeneralSettingsControllers::class,'imageSettings'])->name('image-setting');
            Route::post('/email-setting',[GeneralSettingsControllers::class,'emailSettings'])->name('email-setting');
            Route::post('/e-commerce-setting',[GeneralSettingsControllers::class,'updateData'])->name('e-commerce-setting');
            Route::post('/settings-update', 'GeneralSettingsControllers@updateSettings')->name('settings-update');
        });

        Route::group(['prefix' => '/sliders', 'middleware' => 'auth'], function () {
            Route::get('/', 'SlidersController@index')->name('sliders.index');
            Route::get('/add', 'SlidersController@create');
            Route::post('/', 'SlidersController@store');
            Route::get('/edit/{id}', 'SlidersController@edit');
            Route::post('/update', 'SlidersController@update');
            Route::delete('/destroy/{id}', 'SlidersController@destroy');
        });
        Route::group(['prefix' => '/feedbacks', 'middleware' => 'auth'], function () {
            Route::get('/', 'ProductReviewController@index')->name('feedbacks.index');
            Route::delete('/destroy/{id}', 'ProductReviewController@destroy')->name('feedbacks.destroy');
        });
        Route::get('/home-page', 'HomeController@homePage')->name('home-page');
        Route::group(['prefix' => '/home-page', 'middleware' => 'auth'], function () {
            Route::post('/home-page-update', 'HomeController@updateHomePage')->name('home-page-update');
        });
        Route::get('/vendor-enquiry', 'VendorEnquiryController@index')->name('vendor-enquiry.index');
        Route::delete('/vendor-enquiry/destroy/{id}', 'VendorEnquiryController@destroy');
        Route::post('/forget-password', 'VendorEnquiryController@postEmail')->name('forget-password');    
    });
});

/*Route::get('/admin', 'AdminUserController@index');
Route::get('/admin/adminuser', 'AdminUserController@getadminusers');
Route::get('/admin/adminuser/add', 'AdminUserController@addadminuser');
Route::post('/admin/adminuser/create','AdminUserController@createadminuser');
Route::get('/admin/adminuser/delete/{id}','AdminUserController@deleteadminuser');
Route::get('/admin/adminuser/edit/{id}','AdminUserController@editadminuser');
Route::post('/admin/adminuser/update','AdminUserController@updateadminuser');*/

// Demo routes
Route::get('/clear-cache-all', function() {
    Artisan::call('cache:clear');
    dd("Cache Clear All");

});

Route::get('/admin/datatables', 'AdminUserController@datatables');
Route::get('/ktdatatables', 'AdminUserController@ktDatatables');
Route::get('/select2', 'AdminUserController@select2');
Route::get('/jquerymask', 'AdminUserController@jQueryMask');
Route::get('/icons/custom-icons', 'AdminUserController@customIcons');
Route::get('/icons/flaticon', 'AdminUserController@flaticon');
Route::get('/icons/fontawesome', 'AdminUserController@fontawesome');
Route::get('/icons/lineawesome', 'AdminUserController@lineawesome');
Route::get('/icons/socicons', 'AdminUserController@socicons');
Route::get('/icons/svg', 'AdminUserController@svg');

// Quick search dummy route to display html elements in search dropdown (header search)
Route::get('/quick-search', 'AdminUserController@quickSearch')->name('quick-search');
/* Orders Bulk Data Route*/
Auth::routes();

/*Route::get('/home', [HomeController::class, 'index'])->name('home');*/
/*Route::get('/home', 'AdminUserController@dashboard')->name('dashboard');*/

// Route::get('/send-email/{emailAddress}', function($emailAddress) {     
//     Mail::send('API.email.emailTest', [
//         'email' => $emailAddress,
//         'data' => []
//     ], function ($message) use ($emailAddress) {
//         $message->subject('Local Email Testing');
//         $message->to($emailAddress);
//     });
//     // check for failures
//     if (Mail::failures()) {
//         print_r(Mail::failures());
//         echo 'Error : Mail not sent';
//     } else {
//         echo 'Success';
//     }
// });

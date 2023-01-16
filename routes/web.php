<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
// Route for authentication
Auth::routes();
// LANDING PAGE WEB ROUTES
Route::post('newsletter', 'HomeController@newsletter');
Route::get('/login/{social}', 'Auth\LoginController@redirectToProvider');
Route::get('/login/{social}/callback', 'Auth\LoginController@handleProviderCallback');
Route::post('/change-lang', 'HomeController@changeLanguage');
Route::get('blog', 'HomeController@blog');
Route::get('blog/{slug}', 'HomeController@showpost');
Route::get('/', function () {
    return view('landing.index');
});

/*
|--------------------------------------------------------------------------
OPASOCIAL WEB ROUTE
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for OPASOCIAL. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    ['as' => 'user.', 'namespace' => 'user', 'middleware' => ['auth']],
    // OPASOCIAL USER ROUTES HERE
    function () {
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
        Route::get('/changelogs', 'SyncController@syncIndex');
        Route::get('blog/{id}/show', 'BlogController@show');
        Route::get('/synced-index/data', 'SyncController@syncIndexData');
        Route::get('/test-controller', 'CurrencyController@index');

        Route::get('/order/new', 'OrderController@newOrder')->name('order.new');
        Route::post('/order', 'OrderController@store');
        Route::get('/orders', 'OrderController@index')->name('order.show');
        Route::get('/orders/{order}/cancel', 'OrderController@cancel');
        Route::get('/order/my-favorites', 'OrderController@favorites');
        Route::get('/order/topservices', 'OrderController@topservices');
        Route::get('/order/premiumnew', 'OrderController@premiumOrder');
        Route::get('/order/digitalnew', 'OrderController@digitalOrder');
        Route::post('/orders/search', 'OrderController@searchOrders');
        Route::get('/order/mass-order', 'OrderController@showMassOrderForm');
        Route::post('/order/mass-order', 'OrderController@storeMassOrder');
        Route::get('/orders-index/data', 'OrderController@indexData');
        Route::get('/orders-filter/{status}', 'OrderController@indexFilter');
        Route::get('/orders-filter-ajax/{status}/data', 'OrderController@indexFilterData');
        Route::get('/orders/{order}/refill', 'OrderController@refill');
        Route::get('/orders/{order}/cancel', 'OrderController@cancel');
        Route::post('top/order', 'OrderController@topstore');
        Route::post('/addtofavorite', 'HomeController@addtofavorite');
        // drip feed user side
        Route::get('/dripfeed', 'OpaSocial\DripFeedController@index')->name('dripfeed.show');
        Route::get('/dripfeed-index/data', 'OpaSocial\DripFeedController@indexData');
        Route::get('/dripfeed/{df}/details', 'OpaSocial\DripFeedController@details');
        // auto like user side
        Route::get('/autolike', 'AutoLikeController@index');
        Route::get('/autolike-index/data', 'AutoLikeController@indexData');
        Route::get('/autolike/{al}/details', 'AutoLikeController@details');
        // Childpanel orders user side
        Route::get('/panelorders-index/data', 'OpaSocial/ChildPanelController@indexData');
        Route::get('child-panels/new/order', 'OpaSocial/ChildPanelController@create');
        Route::get('child-panels/orders/sync', 'OpaSocial/ChildPanelController@sync');
        Route::resource('/child-panels', 'OpaSocial/ChildPanelController');
        Route::get('/panels-filter/{status}', 'OpaSocial/ChildPanelController@index');
        Route::get('/panels-filter-ajax/{status}/data', 'OpaSocial/ChildPanelController@indexFilterData');

        Route::get('/service/get-packages/{service_id}', 'OrderController@getPackages');
        Route::get('/service/get-fpackages/{service_id}', 'OrderController@getfPackages');


        Route::get('/broadcast/{cache_id}', 'DashboardController@getBroadCast');
        Route::get('/messages', 'DashboardController@indexMessages');
        Route::get('/payment/add-funds/coupon', 'CouponController@showForm')->name("coupon-form");
        Route::post('/payment/add-funds/coupon', 'CouponController@store')->name("couponcartform");


        Route::put('/redeem', 'AccountController@redeemPoints');
        Route::get('/points-history', 'AccountController@getRedeemHistory');
        Route::get('/points-history-index/data', 'AccountController@getRedeemHistoryData');
        Route::resource('/blog', 'BlogController');

        Route::get('/subscriptions', 'SubscriptionController@index')->name('subscription.show');
        Route::get('/subscriptions/{id}', 'SubscriptionController@show');
        Route::get('/subscriptions-index/data', 'SubscriptionController@indexData');
        Route::get('/subscription/new', 'SubscriptionController@create');
        Route::post('/subscription', 'SubscriptionController@store');

        Route::get('/account/settings', 'AccountController@showSettings');
        Route::put('/account/password', 'AccountController@updatePassword');
        Route::put('/account/config', 'AccountController@updateConfig');
        Route::get('/account/update', 'AccountController@updateKey');
        Route::post('/account/api', 'AccountController@generateKey');
        Route::post('/account/api1', 'HomeController@generateKey');
        Route::get('/account/funds-load-history', 'AccountController@getFundsLoadHistory')->name('fund-load-history');
        Route::get('account/funds-load-history-index/data', 'AccountController@getFundsLoadHistoryData');
        Route::get('/payment/add-funds', 'PaymentController@getPaymentMethods');


        Route::get('/rave/callback', 'FlutterWaveController@callback');
        Route::get('/payment/add-funds/flutterwave', 'FlutterWaveController@showForm');
        Route::get('/payment/add-funds/flutterwave/success', 'FlutterWaveController@success');
        Route::get('/payment/add-funds/flutterwave/cancel', 'FlutterWaveController@cancel');
        Route::post('/payment/add-funds/flutterwave', 'FlutterWaveController@store');

        Route::get('/payment/add-funds/bitcoin', 'CoinPaymentsController@showForm');
        Route::post('/payment/add-funds/bitcoin', 'CoinPaymentsController@store');
        Route::get('/payment/add-funds/bitcoin/cancel', 'CoinPaymentsController@cancel');
        Route::get('/payment/add-funds/bitcoin/success', 'CoinPaymentsController@success');

        Route::get('/payment/add-funds/bank-other', 'HomeController@showManualPaymentForm');




        Route::get('/backof', 'DashboardController@loginBack');

        Route::get('/support', 'SupportController@index');
        Route::get('/support-index/data', 'SupportController@indexData');
        Route::get('/support/ticket/create', 'SupportController@create');
        Route::post('/support/ticket/store', 'SupportController@store');
        Route::get('/support/ticket/{id}', 'SupportController@show');
        Route::post('/support/{id}/message', 'SupportController@message');
    }
);

Route::group(
    ['as' => 'moderator.', 'prefix' => 'moderator', 'namespace' => 'Moderator', 'middleware' => ['auth', 'moderator']],
    // OPASOCIAL MODERATOR ROUTES HERE
    function () {
        Route::resource('/', 'SupportController');

        Route::get('/account/settings', 'AccountController@showSettings');
        Route::get('/account/settings', 'AccountController@showSettings');
        Route::put('/account/password', 'AccountController@updatePassword');

        Route::resource('/orders', 'OrderController');
        Route::post('/order/{id}/complete', 'OrderController@completeOrder');
        Route::get('/orders-ajax/data', 'OrderController@indexData');
        Route::post('/orders-bulk-update', 'OrderController@bulkUpdate');
        Route::get('/orders-filter/{status}', 'OrderController@indexFilter');
        Route::get('/orders-filter-ajax/{status}/data', 'OrderController@indexFilterData');
        Route::get('/dripfeed', 'DripFeedController@index');
        Route::get('/dripfeed-index/data', 'DripFeedController@indexData');
        Route::get('/dripfeed/edit/{dripfeed}', 'DripFeedController@edit');
        Route::post('/dripfeed', 'DripFeedController@update');
        Route::get('/dripfeed/{df}/details', 'DripFeedController@details');

        Route::get('/autolike', 'AutoLikeController@index');
        Route::get('/autolike-index/data', 'AutoLikeController@indexData');
        Route::get('/autolike/edit/{autolike}', 'AutoLikeController@edit');
        Route::post('/autolike', 'AutoLikeController@update');
        Route::get('/autolike/{al}/details', 'AutoLikeController@details');


        Route::resource('/support/tickets', 'SupportController');
        Route::post('/support/{id}/message', 'SupportController@message');
        Route::get('/ticket-filter/{topic}', 'SupportController@indexFilter');
        Route::get('/ticket-filter-ajax/{topic}/data', 'SupportController@indexFilterData');
        Route::get('/ticket-new/{topic}', 'SupportController@indexNFilter');
        Route::get('/ticket-new-ajax/{topic}/data', 'SupportController@indexNFilterData');
        Route::get('/orders-index/data', 'SupportController@indexData');

        Route::get('/funds-load-history', 'UserController@getFundsLoadHistory');
        Route::get('/funds-load-history/data', 'UserController@getFundsLoadHistoryData');
        Route::get('/refills/list', 'RefillRequestController@index');
        Route::get('/refills-ajax/data', 'RefillRequestController@indexData');
        Route::get('/refill-complete', 'RefillRequestController@completeStatus');
        Route::get('/refills-filter/{status}', 'RefillRequestController@indexFilter');
        Route::get('/refills-filter-ajax/{status}/data', 'RefillRequestController@indexFilterData');
        Route::get('/refills/{order}/details', 'RefillRequestController@details');
        Route::get('/refill/{refill}/{status}', 'RefillRequestController@changeStatus');
        Route::get('/system/refresh', 'DashboardController@refreshSystem');
    }
);

Route::group(
    ['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'moderator', 'admin']],
    // OPASOCIAL ADMIN ROUTES HERE
    function () {
        Route::get('/cashes', 'CashController@index');
        Route::resource('/ips', 'IpsController');
        Route::get('/ips-ajax/data', 'IpsController@indexData');
        Route::get('/', 'DashboardController@index');
        Route::get('/dash', 'DashboardController@indexdash');
        Route::post('/note', 'DashboardController@saveNote');
        Route::get('/account/settings', 'AccountController@showSettings');
        Route::put('/account/password', 'AccountController@updatePassword');
        Route::get('/profit', 'DPController@profit');
        Route::get('/mprofit', 'DPController@wprofit');
        Route::get('/package/{package}/up', 'PackageController@up');
        Route::get('/package/{package}/down', 'PackageController@down');
        Route::resource('/services', 'ServiceController');

        Route::resource('/ads', 'AdsController');
        Route::get('/ads-ajax/data', 'AdsController@indexData');
        Route::resource('/newsletter', 'NewsletterController');
        Route::get('/export/newsletter', 'NewsletterController@export');
        Route::get('/newsletter-ajax/data', 'NewsletterController@indexData');
        Route::post('/services/sequence', 'ServiceController@saveSequence');
        Route::post('/packages/sequence', 'ServiceController@packageSequence');


        Route::get('/top-services', 'ServiceController@getTopServices');
        Route::get('/packages/top/{id}/edit', 'ServiceController@editTopServices');
        Route::post('topservice/store', 'ServiceController@storeTopServices');
        Route::delete('packages/top/{id}', 'ServiceController@deleteTopServices');
        Route::get('/top-services/create', 'ServiceController@createTopServices');
        Route::get('/top-services-ajax/data', 'ServiceController@TopindexData');
        Route::get('/services-index/data', 'ServiceController@indexData');
        Route::get('/codes', 'LicenseCodeController@index');
        Route::get('/codes-ajax/data', 'LicenseCodeController@indexData');
        Route::get('/service/{service}/up', 'ServiceController@ups');
        Route::get('/service/{service}/down', 'ServiceController@downs');
        Route::get('/service/package/{package}/up', 'ServiceController@up');
        Route::get('/service/package/{package}/down', 'ServiceController@down');
        Route::get('/services/{service}/details', 'ServiceController@details');
        Route::get('/active/{service}/{package}', 'ServiceController@activeSP');
        Route::get('/inactive/{service}/{package}', 'ServiceController@inactiveSP');
        Route::get('/delete/{service}/{package}', 'ServiceController@deleteSP');
        Route::get('/downloads', 'OrderController@downloads');
        Route::get('/downloadrecords-ajax/data', 'OrderController@downloadsindexData');
        Route::get('/systeminfo', 'ConfigController@index');
        Route::resource('/child-panels', 'ChildPanelController');
        Route::get('child-panels/orders/', 'ChildPanelController@show');
        Route::get('child-panels/orders/sync', 'ChildPanelController@sync');
        Route::put('child-panels/order/{id}', 'ChildPanelController@updateorder');
        Route::get('child-panels-orders/data', 'ChildPanelController@getorders');
        Route::post('/child-panels/update/price', 'ChildPanelController@updatePrice');
        Route::get('/system/settings', 'ConfigController@edit');
        Route::put('/system/settings', 'ConfigController@update');
        Route::get('/users/{id}/login', 'UserController@loginAs');

        Route::resource('/payment-methods', 'PaymentMethodController');
        Route::resource('/commission', 'CommissionController');
        Route::resource('/popup-notification', 'PopupNotificationController');
        Route::get('/affiliate_transactions', 'CommissionController@affiliate_transaction');
        Route::get('/remove_table/{id}', 'CommissionController@removetable');

        Route::get('/fund-records/{id}', 'UserController@newFundIndex');
        Route::get('/fund-records/data/{id}', 'UserController@indexFundData');
        Route::get('/blog-ajax/data', 'BlogController@indexData');
        Route::resource('/blog', 'BlogController');
        Route::get('blog/{id}/show', 'BlogController@show');
        Route::get('/apifetch', 'FetchController@index');
        Route::post('/apifetch/show', 'FetchController@showData');
        Route::get('/apifetch/getmap', 'FetchController@getMap');
        Route::post('/apifetch/savemap', 'FetchController@saveMap');
        Route::get('/apifetch/data', 'FetchController@getData');
        Route::post('/apifetch/save', 'FetchController@saveData');
        Route::get('/apifetch/redirect', 'FetchController@redirect');

        Route::get('/mail', 'SyncController@mail');
        Route::get('/synced', 'SyncController@syncIndex');
        Route::get('/synced-index/data', 'SyncController@syncIndexData');
        Route::resource('/services', 'ServiceController');
        Route::get('/services-index/data', 'ServiceController@indexData');
        Route::resource('/currency', 'CurrencyController');
        Route::get('/currency-data', 'CurrencyController@IndexData');
        Route::get('/currency/edit/{id}', 'CurrencyController@edit');
        Route::delete('/currency/delete/{id}', 'CurrencyController@destroy');
        Route::get('/currency/create', 'CurrencyController@create');
        Route::post('/currency/store', 'CurrencyController@store');
        Route::resource('/packages', 'PackageController');
        Route::get('/packages-index/data', 'PackageController@indexData');
        Route::post('/packages/ajax', 'PackageController@ajaxPost');
        Route::post('/packages/ajax/{id}', 'PackageController@update');
        Route::post('/packages/create', 'PackageController@ajaxPost');

        Route::post('/users/package-special-prices/{id}', 'UserController@packageSpecialPrices');
        Route::resource('/users', 'UserController');
        Route::resource('/groups', 'GroupController');
        Route::post('/users/add-funds/{id}', 'UserController@addFunds');
        Route::get('/users-ajax/data', 'UserController@indexData');
        Route::get('/groups-ajax/data', 'GroupController@indexData');
        Route::get('/users-referralajax/data', 'UserController@referralindexData');
        Route::get('/referral', 'UserController@referralindex');

        Route::post('/add-funds/admin', 'UserController@addFundsAdmin');
        Route::post('order/search', 'OrderController@orderSearch');
        Route::get('order/search', 'OrderController@orderSearch');
        Route::post('/pending-orders-bulk-update', 'AutomateController@bulkUpdatePending');

        Route::get('/dripfeed', 'DripFeedController@index');
        Route::get('/dripfeed-index/data', 'DripFeedController@indexData');
        Route::get('/dripfeed/edit/{dripfeed}', 'DripFeedController@edit');
        Route::post('/dripfeed', 'DripFeedController@update');
        Route::get('/dripfeed/{df}/details', 'DripFeedController@details');
        Route::get('/orders-filters/manual', 'OrderController@indexmanual');
        Route::get('/orders-filters-ajax/manual/data', 'OrderController@indexDataManual');
        Route::get('/autolike', 'AutoLikeController@index');
        Route::get('/autolike-index/data', 'AutoLikeController@indexData');
        Route::get('/autolike/edit/{autolike}', 'AutoLikeController@edit');
        Route::post('/autolike', 'AutoLikeController@update');
        Route::get('/autolike/{al}/details', 'AutoLikeController@details');

        Route::get('/points-history', 'UserController@getRedeemHistory');
        Route::get('/points-history/data', 'UserController@getRedeemHistoryData');
        Route::put('/users/point-funds/{id}', 'UserController@redeemAccept');
        Route::put('/users/point-fundsreject/{id}', 'UserController@redeemReject');
        Route::resource('/orders', 'OrderController');
        Route::post('/order/{id}/complete', 'OrderController@completeOrder');
        Route::get('/orders-ajax/data', 'OrderController@indexData');
        Route::post('/orders-bulk-update', 'OrderController@bulkUpdate');
        Route::get('/orders-filter/{status}', 'OrderController@indexFilter');
        Route::get('/orders-filter-ajax/{status}/data', 'OrderController@indexFilterData');

        Route::get('/subscriptions', 'SubscriptionController@index');
        Route::get('/subscriptions-index/data', 'SubscriptionController@indexData');
        Route::get('/subscriptions/{id}/edit', 'SubscriptionController@edit');
        Route::post('/subscriptions/{id}', 'SubscriptionController@update');
        Route::put('/subscriptions/{id}/cancel', 'SubscriptionController@cancel');
        Route::put('/subscriptions/{id}/stop', 'SubscriptionController@stop');
        Route::get('/subscriptions/{id}/orders', 'SubscriptionController@orders');
        Route::post('/subscriptions/{id}/order', 'SubscriptionController@storeOrder');
        Route::get('/subscriptions-filter/{status}', 'SubscriptionController@indexFilter');
        Route::get('/subscriptions-filter-ajax/{status}/data', 'SubscriptionController@indexFilterData');

        Route::get('/broadcast', 'BroadcastController@index');
        Route::get('/broadcast-index/data', 'BroadcastController@indexData');
        Route::get('/broadcast/create', 'BroadcastController@create');
        Route::get('/broadcast/{id}', 'BroadcastController@edit');
        Route::get('/broadcast/delete/{id}', 'BroadcastController@destroy');
        Route::post('/broadcast/{id}/update', 'BroadcastController@update');
        Route::post('/broadcast', 'BroadcastController@addfunc');

        Route::get('/users/{user}/message', 'UserController@message');
        Route::post('/users/message', 'UserController@postmessage');
        Route::delete('support/message/destroy/{id}', 'SupportController@destroyMsg');
        Route::post('support/message/{id}', 'SupportController@editMessage');
        Route::resource('/support/tickets', 'SupportController');
        Route::get('/support/tickets/{id}/close', 'SupportController@close');
        Route::post('/support/{id}/message', 'SupportController@message');
        Route::get('/orders-index/data', 'SupportController@indexData');
        Route::get('/ticket-filter/{topic}', 'SupportController@indexFilter');
        Route::get('/ticket-filter-ajax/{topic}/data', 'SupportController@indexFilterData');
        Route::get('/ticket-new/{topic}', 'SupportController@indexNFilter');
        Route::get('/ticket-new-ajax/{topic}/data', 'SupportController@indexNFilterData');

        Route::get('/funds-load-history', 'UserController@getFundsLoadHistory');
        Route::get('/funds-load-history/data', 'UserController@getFundsLoadHistoryData');

        Route::get('/pages', 'PageController@index');
        Route::get('/page-edit/{slug}', 'PageController@edit');
        Route::put('/page-edit/{id}', 'PageController@update');
        Route::get('/automate/api/addmxz', 'AutomateController@addApimxz');
        Route::post('/automate/api/addmxz', 'AutomateController@storeApimxz');
        Route::get('/automate/api/addperfectpanel', 'AutomateController@addApiperfect');
        Route::post('/automate/api/addperfectpanel', 'AutomateController@storeApiperfect');
        Route::get('/automate/api-list', 'AutomateController@listApi');
        Route::get('/automate/send-orders', 'AutomateController@sendOrdersIndex');
        Route::get('/automate/send-orders-index/data', 'AutomateController@sendOrdersIndexData');
        Route::post('/automate/send-order-to-api', 'AutomateController@sendOrderToApi');

        Route::get('/automate/response-logs', 'AutomateController@getResponseLogsIndex');
        Route::get('/automate/response-logs-index/data', 'AutomateController@getResponseLogsIndexData');
        Route::get('notification/{id}', 'OrderController@notification');

        Route::get('/refills/list', 'RefillRequestController@index');
        Route::get('/refills-ajax/data', 'RefillRequestController@indexData');
        Route::get('/refills-filter/{status}', 'RefillRequestController@indexFilter');
        Route::get('/refills-filter-ajax/{status}/data', 'RefillRequestController@indexFilterData');
        Route::get('/refills/{order}/details', 'RefillRequestController@details');
        Route::get('/refill/{refill}/{status}', 'RefillRequestController@changeStatus');

        Route::get('/automate/api/add', 'AutomateController@addApi');
        Route::post('/automate/api/add', 'AutomateController@storeApi');
        Route::get('/automate/api/{id}/edit', 'AutomateController@editApi');
        Route::delete('/automate/api/{id}', 'AutomateController@deleteApi');
        Route::put('/automate/api/{id}', 'AutomateController@updateApi');
        Route::post('/automate/api/mapping/{id}', 'AutomateController@storeMapping');
        //Copuon routes
        Route::get('/coupons', 'CouponController@index');
        Route::get('/coupons/create', 'CouponController@create');
        Route::get('/coupons/{id}/edit', 'CouponController@edit');
        Route::put('/coupons/{id}/update', 'CouponController@update');
        Route::post('/coupons/store', 'CouponController@store');
        Route::get('/coupons/history', 'CouponController@history');
        Route::delete('/coupons/{id}', 'CouponController@destroy');
        Route::delete('/coupons/history/{id}', 'CouponController@destroyhistory');
        Route::get('/coupons-ajax/data', 'CouponController@indexData');
        Route::get('/coupons-history-ajax/data', 'CouponController@history');
        Route::get('/refill-complete', 'RefillRequestController@completeStatus');

        Route::get('/automate/get-status', 'AutomateController@getOrderStatusIndex');
        Route::get('/automate/get-status-index/data', 'AutomateController@getOrderStatusIndexData');
        Route::post('/automate/get-status-from-api', 'AutomateController@getOrderStatusFromAPI');
        Route::post('/automate/change-reseller', 'AutomateController@changeReseller');

        Route::get('/system/refresh', 'DashboardController@refreshSystem');
    }
);

/*
|--------------------------------------------------------------------------
OPAVERIFY WEB ROUTE
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for OPASOCIAL. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    ['as' => 'user.', 'namespace' => 'user', 'middleware' => ['auth']],
    function () {
        // OPAVERIFY USER ROUTES HERE
    }
);

Route::group(
    ['as' => 'moderator.', 'prefix' => 'moderator', 'namespace' => 'Moderator', 'middleware' => ['auth', 'moderator']],
    function () {
        // OPAVERIFY MODERATOR ROUTES HERE
    }
);

Route::group(
    ['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'moderator', 'admin']],
    function () {
        // OPAVERIFY ADMIN ROUTES HERE

    }
);

<?php

use App\Mail\MailTest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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
Route::get('/howitwork', 'HomeController@indexhiw');
Route::get('ref/{name}/{uid}', 'VisitController@index');

Route::post('newsletter', 'HomeController@newsletter');
Route::get('/login/{social}', 'Auth\LoginController@redirectToProvider');
Route::get('/login/{social}/callback', 'Auth\LoginController@handleProviderCallback');
Route::post('/change-lang', 'HomeController@changeLanguage');
Route::get('blog', 'HomeController@blog');
Route::get('blog/{slug}', 'HomeController@showpost');
Route::get('/services', 'HomeController@showServices');
// Route::get('blog/{id}/show', 'BlogController@show');

// Route::get('/', function () {
//     return view('landing.index');
// });

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

        Route::get('/', 'OpaSocial\OrderController@newOrder')->name('order.new');
        // Route::get('/order/new', 'OpaSocial\OrderController@newOrder')->name('order.new');
        Route::post('/orders', 'OpaSocial\OrderController@store')->name('route.store');
        Route::get('/orders', 'OpaSocial\OrderController@index')->name('order.show');
        Route::get('/orders/filter/{status}', 'OpaSocial\OrderController@ordersFilter')->name('order.filter');
        Route::get('/orders/search', 'OpaSocial\OrderController@searchOrders')->name('order.search');

        // Route::get('/order-details/{orderID}', 'OpaSocial\OrderController@OrderDetailsmodal')->name('order.details.modal');


        // Route::post('/orders', 'OpaSocial\OrderController@store');
        // Order Vue routes
        Route::get('orders/category', 'OpaSocial\OrderController@getOrderCategory')->name('get.order.category');
        Route::get('orders/service/{category}', 'OpaSocial\OrderController@getOrderService')->name('get.order.service');
        Route::get('orders/convert/{fund}', 'OpaSocial\OrderController@convertOrderPrice')->name('convert.order.price');

        Route::get('massorder', 'OpaSocial\OrderController@showMassOrderForm')->name('massorder');
        Route::post('/order/mass-order', 'OpaSocial\OrderController@storeMassOrder');
        Route::get('/orders/{order}/cancel', 'OpaSocial\OrderController@cancel');
        Route::get('/order/my-favorites', 'OpaSocial\OrderController@favorites');
        Route::get('/order/topservices', 'OpaSocial\OrderController@topservices');
        Route::get('/order/premiumnew', 'OpaSocial\OrderController@premiumOrder');
        Route::get('/order/digitalnew', 'OpaSocial\OrderController@digitalOrder');

        Route::get('/orders-index/data', 'OpaSocial\OrderController@indexData');
        Route::get('/orders-filter-ajax/{status}/data', 'OpaSocial\OrderController@indexFilterData');
        Route::get('/orders/{order}/refill', 'OpaSocial\OrderController@refill');
        Route::get('/orders/{order}/cancel', 'OpaSocial\OrderController@cancel');
        Route::post('top/order', 'OpaSocial\OrderController@topstore');



        // Tickets
        Route::get('/ticket', 'SupportController@index')->name('ticket.index');
        Route::get('/ticket/create', 'SupportController@create')->name('ticket.create');
        Route::post('/support/ticket/store', 'SupportController@store')->name('ticket.store');
        Route::get('ticket/{id}/message', 'SupportController@show')->name('ticket.show');
        Route::post('/ticket/{id}/message', 'SupportController@message')->name('ticket.message');
        // Route::get('/support-index/data', 'SupportController@indexData')->name('ticket.data');


        Route::get('/support/tick', function () {
            if (Mail::to('info@opasocial.com')->send(new MailTest())) {
                dd('Sent');
            }
            dd('Not Sent');
        });








        // Services controller
        Route::get('/services', 'OpaSocial\ServiceController@showServices')->name('services.show');
        Route::post('/services/search', 'HomeController@searchServices');
        Route::post('/addtofavorite', 'OpaSocial\ServiceController@addtofavorite');
        Route::get('/addtofavoritetest/{pid}', 'OpaSocial\ServiceController@addtofavoritetest');

        // drip feed user side
        Route::get('/dripfeeds', 'OpaSocial\DripFeedController@index')->name('dripfeed.show');
        Route::get('/dripfeed-index/data', 'OpaSocial\DripFeedController@indexData');
        Route::get('/dripfeed/{df}/details', 'OpaSocial\DripFeedController@details');
        // auto like user side
        Route::get('/autolike', 'AutoLikeController@index');
        Route::get('/autolike-index/data', 'AutoLikeController@indexData');
        Route::get('/autolike/{al}/details', 'AutoLikeController@details');
        // Childpanel orders user side
        Route::get('/child-panel', 'OpaSocial\ChildPanelController@index')->name('childpanel.show');
        Route::post('/child-panel', 'OpaSocial\ChildPanelController@create')->name('childpanel.create');

        // Route::resource('/child-panel', 'OpaSocial\ChildPanelController');
        Route::get('/panelorders-index/data', 'OpaSocial\ChildPanelController@indexData');
        Route::get('child-panels/new/order', 'OpaSocial\ChildPanelController@create');
        Route::get('child-panels/orders/sync', 'OpaSocial\ChildPanelController@sync');
        Route::get('/panels-filter/{status}', 'OpaSocial\ChildPanelController@index');
        Route::get('/panels-filter-ajax/{status}/data', 'OpaSocial\ChildPanelController@indexFilterData');

        Route::get('/service/get-packages/{service_id}', 'OpaSocial\OrderController@getPackages');
        Route::get('/service/get-fpackages/{service_id}', 'OpaSocial\OrderController@getfPackages');


        Route::get('/broadcast/{cache_id}', 'DashboardController@getBroadCast');
        Route::get('/messages', 'DashboardController@indexMessages');
        Route::get('/payment/add-funds/coupon', 'CouponController@showForm')->name("coupon-form");
        Route::post('/payment/add-funds/coupon', 'CouponController@store')->name("couponcartform");


        Route::put('/redeem', 'AccountController@redeemPoints');
        Route::get('/points-history', 'AccountController@getRedeemHistory');
        Route::get('/points-history-index/data', 'AccountController@getRedeemHistoryData');

        Route::get('/subscriptions', 'OpaSocial\SubscriptionController@index')->name('subscription.show');
        Route::get('/subscriptions/{id}', 'OpaSocial\SubscriptionController@show');
        Route::get('/subscriptions-index/data', 'OpaSocial\SubscriptionController@indexData');
        Route::get('/subscription/new', 'OpaSocial\SubscriptionController@create');
        Route::post('/subscription', 'OpaSocial\SubscriptionController@store');

        Route::get('/account/settings', 'AccountController@showSettings');
        Route::put('/account/password', 'AccountController@updatePassword');
        Route::put('/account/config', 'AccountController@updateConfig');
        Route::get('/account/update', 'AccountController@updateKey');
        Route::post('/account/api', 'AccountController@generateKey');
        Route::post('/account/api1', 'HomeController@generateKey');
        Route::get('/account/funds-load-history', 'AccountController@getFundsLoadHistory')->name('fund-load-history');
        Route::get('account/funds-load-history-index/data', 'AccountController@getFundsLoadHistoryData');
        Route::get('/payment/add-funds', 'PaymentController@getPaymentMethods')->name('add-funds');


        Route::get('/rave/callback', 'FlutterWaveController@callback');
        Route::get('/payment/add-funds/flutterwave', 'FlutterWaveController@showForm');
        Route::get('/payment/add-funds/flutterwave/success', 'FlutterWaveController@success');
        Route::get('/payment/add-funds/flutterwave/cancel', 'FlutterWaveController@cancel');
        Route::post('/payment/add-funds/flutterwave', 'FlutterWaveController@store');

        // Route::get('/payment/add-funds/bitcoin', 'CoinPaymentsController@showForm');
        // Route::post('/payment/add-funds/bitcoin', 'CoinPaymentsController@store');
        // Route::get('/payment/add-funds/bitcoin/cancel', 'CoinPaymentsController@cancel');
        // Route::get('/payment/add-funds/bitcoin/success', 'CoinPaymentsController@success');

        Route::get('/payment/add-funds/bank-other', 'HomeController@showManualPaymentForm');




        Route::get('/backof', 'DashboardController@loginBack');




        // Home Controller general
        Route::get('/makemoney', 'HomeController@indexmakemoney')->name('make.money');
        Route::get('/faqs', 'HomeController@faqs')->name('faqs');
        Route::get('/api', 'HomeController@api')->name('api');

        Route::get('/servicetracker', 'HomeController@packagetracker');
        Route::get('/servicetracker/data', 'HomeController@packagetrackerindexData');
        Route::post('/servicetracker/search', 'HomeController@searchServicetracker');
        // Affiliate
        Route::get('/affiliates', 'ReferralController@showAffiliates')->name('affiliate.show');
        //    Route::get('/remove_table/{id}','ReferralController@removetable');
        // Route::group(['middleware' => 'VerifyModuleAPIEnabled'], function () {
        //        Route::get('/api', 'HomeController@ApiDocV2');
        //        Route::get('/api-v1', 'HomeController@ApiDocV1');
        //     });
        Route::get('/check', function () {
            $exitCode = Artisan::call('status:check');
        });
        Route::get('/perf', function () {
            $exitCode = Artisan::call('check:perf');
        });
        Route::get('/send', function () {
            $exitCode = Artisan::call('orders:send');
        });
        Route::get('/drip', function () {
            $exitCode = Artisan::call('drip:feed');
        });
        Route::get('/like', function () {
            $exitCode = Artisan::call('auto:like');
        });
        Route::get('/syncseller', function () {
            $exitCode = Artisan::call('seller:sync');
        });
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

        Route::resource('/orders', 'OpaSocial\OrderController');
        Route::post('/order/{id}/complete', 'OpaSocial\OrderController@completeOrder');
        Route::get('/orders-ajax/data', 'OpaSocial\OrderController@indexData');
        Route::post('/orders-bulk-update', 'OpaSocial\OrderController@bulkUpdate');
        Route::get('/orders-filter/{status}', 'OpaSocial\OrderController@indexFilter');
        Route::get('/orders-filter-ajax/{status}/data', 'OpaSocial\OrderController@indexFilterData');
        Route::get('/dripfeed', 'OpaSocial\DripFeedController@index');
        Route::get('/dripfeed-index/data', 'OpaSocial\DripFeedController@indexData');
        Route::get('/dripfeed/edit/{dripfeed}', 'OpaSocial\DripFeedController@edit');
        Route::post('/dripfeed', 'OpaSocial\DripFeedController@update');
        Route::get('/dripfeed/{df}/details', 'OpaSocial\DripFeedController@details');

        Route::get('/autolike', 'OpaSocial\AutoLikeController@index');
        Route::get('/autolike-index/data', 'OpaSocial\AutoLikeController@indexData');
        Route::get('/autolike/edit/{autolike}', 'OpaSocial\AutoLikeController@edit');
        Route::post('/autolike', 'OpaSocial\AutoLikeController@update');
        Route::get('/autolike/{al}/details', 'OpaSocial\AutoLikeController@details');


        Route::resource('/support/tickets', 'SupportController');
        Route::post('/support/{id}/message', 'SupportController@message');
        Route::get('/ticket-filter/{topic}', 'SupportController@indexFilter');
        Route::get('/ticket-filter-ajax/{topic}/data', 'SupportController@indexFilterData');
        Route::get('/ticket-new/{topic}', 'SupportController@indexNFilter');
        Route::get('/ticket-new-ajax/{topic}/data', 'SupportController@indexNFilterData');
        Route::get('/orders-index/data', 'SupportController@indexData');

        Route::get('/funds-load-history', 'UserController@getFundsLoadHistory');
        Route::get('/funds-load-history/data', 'UserController@getFundsLoadHistoryData');
        Route::get('/refills/list', 'OpaSocial\RefillRequestController@index');
        Route::get('/refills-ajax/data', 'OpaSocial\RefillRequestController@indexData');
        Route::get('/refill-complete', 'OpaSocial\RefillRequestController@completeStatus');
        Route::get('/refills-filter/{status}', 'OpaSocial\RefillRequestController@indexFilter');
        Route::get('/refills-filter-ajax/{status}/data', 'OpaSocial\RefillRequestController@indexFilterData');
        Route::get('/refills/{order}/details', 'OpaSocial\RefillRequestController@details');
        Route::get('/refill/{refill}/{status}', 'OpaSocial\RefillRequestController@changeStatus');
        // Route::get('/system/refresh', 'DashboardController@refreshSystem');
    }
);

Route::group(
    ['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'moderator', 'admin']],
    // OPASOCIAL ADMIN ROUTES HERE
    function () {
        Route::resource('/blog', 'BlogController');
        Route::get('/test-controller', 'CurrencyController@index');
        // Route::post('/change-currency', 'HomeController@changeCurrency');

        Route::get('/cashes', 'CashController@index');
        Route::resource('/ips', 'IpsController');
        Route::get('/ips-ajax/data', 'IpsController@indexData');
        Route::get('/', 'DashboardController@index');
        Route::get('/dash', 'DashboardController@indexdash');
        Route::post('/note', 'DashboardController@saveNote');
        Route::get('/account/settings', 'AccountController@showSettings');
        Route::put('/account/password', 'AccountController@updatePassword');
        Route::get('/profit', 'OpaSocial\DPController@profit');
        Route::get('/mprofit', 'OpaSocial\DPController@wprofit');
        Route::get('/package/{package}/up', 'OpaSocial\PackageController@up');
        Route::get('/package/{package}/down', 'OpaSocial\PackageController@down');
        Route::resource('/services', 'OpaSocial\ServiceController');

        Route::resource('/ads', 'AdsController');
        Route::get('/ads-ajax/data', 'AdsController@indexData');
        Route::resource('/newsletter', 'NewsletterController');
        Route::get('/export/newsletter', 'NewsletterController@export');
        Route::get('/newsletter-ajax/data', 'NewsletterController@indexData');
        Route::post('/services/sequence', 'OpaSocial\ServiceController@saveSequence');
        Route::post('/packages/sequence', 'OpaSocial\ServiceController@packageSequence');


        Route::get('/top-services', 'OpaSocial\ServiceController@getTopServices');
        Route::get('/packages/top/{id}/edit', 'OpaSocial\ServiceController@editTopServices');
        Route::post('topservice/store', 'OpaSocial\ServiceController@storeTopServices');
        Route::delete('packages/top/{id}', 'OpaSocial\ServiceController@deleteTopServices');
        Route::get('/top-services/create', 'OpaSocial\ServiceController@createTopServices');
        Route::get('/top-services-ajax/data', 'OpaSocial\ServiceController@TopindexData');
        Route::get('/services-index/data', 'OpaSocial\ServiceController@indexData');
        Route::get('/codes', 'LicenseCodeController@index');
        Route::get('/codes-ajax/data', 'LicenseCodeController@indexData');
        Route::get('/service/{service}/up', 'OpaSocial\ServiceController@ups');
        Route::get('/service/{service}/down', 'OpaSocial\ServiceController@downs');
        Route::get('/service/package/{package}/up', 'OpaSocial\ServiceController@up');
        Route::get('/service/package/{package}/down', 'OpaSocial\ServiceController@down');
        Route::get('/services/{service}/details', 'OpaSocial\ServiceController@details');
        Route::get('/active/{service}/{package}', 'OpaSocial\ServiceController@activeSP');
        Route::get('/inactive/{service}/{package}', 'OpaSocial\ServiceController@inactiveSP');
        Route::get('/delete/{service}/{package}', 'OpaSocial\ServiceController@deleteSP');
        Route::get('/downloads', 'OpaSocial\OrderController@downloads');
        Route::get('/downloadrecords-ajax/data', 'OpaSocial\OrderController@downloadsindexData');
        Route::get('/systeminfo', 'ConfigController@index');

        // Child panel
        Route::resource('/child-panels', 'OpaSocial\ChildPanelController');
        Route::get('child-panels/orders/', 'OpaSocial\ChildPanelController@show');
        Route::get('child-panels/orders/sync', 'OpaSocial\ChildPanelController@sync');
        Route::put('child-panels/order/{id}', 'OpaSocial\ChildPanelController@updateorder');
        Route::get('child-panels-orders/data', 'OpaSocial\ChildPanelController@getorders');
        Route::post('/child-panels/update/price', 'OpaSocial\ChildPanelController@updatePrice');
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
        Route::get('/apifetch', 'OpaSocial\FetchController@index');
        Route::post('/apifetch/show', 'OpaSocial\FetchController@showData');
        Route::get('/apifetch/getmap', 'OpaSocial\FetchController@getMap');
        Route::post('/apifetch/savemap', 'OpaSocial\FetchController@saveMap');
        Route::get('/apifetch/data', 'OpaSocial\FetchController@getData');
        Route::post('/apifetch/save', 'OpaSocial\FetchController@saveData');
        Route::get('/apifetch/redirect', 'OpaSocial\FetchController@redirect');

        Route::get('/mail', 'OpaSocial\SyncController@mail');
        Route::get('/synced', 'OpaSocial\SyncController@syncIndex');
        Route::get('/synced-index/data', 'OpaSocial\SyncController@syncIndexData');
        Route::resource('/services', 'OpaSocial\ServiceController');
        Route::get('/services-index/data', 'OpaSocial\ServiceController@indexData');
        Route::resource('/currency', 'CurrencyController');
        Route::get('/currency-data', 'CurrencyController@IndexData');
        Route::get('/currency/edit/{id}', 'CurrencyController@edit');
        Route::delete('/currency/delete/{id}', 'CurrencyController@destroy');
        Route::get('/currency/create', 'CurrencyController@create');
        Route::post('/currency/store', 'CurrencyController@store');
        Route::resource('/packages', 'OpaSocial\PackageController');
        Route::get('/packages-index/data', 'OpaSocial\PackageController@indexData');
        Route::post('/packages/ajax', 'OpaSocial\PackageController@ajaxPost');
        Route::post('/packages/ajax/{id}', 'OpaSocial\PackageController@update');
        Route::post('/packages/create', 'OpaSocial\PackageController@ajaxPost');

        Route::post('/users/package-special-prices/{id}', 'UserController@packageSpecialPrices');
        Route::resource('/users', 'UserController');
        Route::resource('/groups', 'OpaSocial\GroupController');
        Route::post('/users/add-funds/{id}', 'UserController@addFunds');
        Route::get('/users-ajax/data', 'UserController@indexData');
        Route::get('/groups-ajax/data', 'OpaSocial\GroupController@indexData');
        Route::get('/users-referralajax/data', 'UserController@referralindexData');
        Route::get('/referral', 'UserController@referralindex');

        Route::post('/add-funds/admin', 'UserController@addFundsAdmin');
        Route::post('order/search', 'OpaSocial\OrderController@orderSearch');
        Route::get('order/search', 'OpaSocial\OrderController@orderSearch');
        Route::post('/pending-orders-bulk-update', 'OpaSocial\AutomateController@bulkUpdatePending');

        Route::get('/dripfeed', 'OpaSocial\DripFeedController@index');
        Route::get('/dripfeed-index/data', 'OpaSocial\DripFeedController@indexData');
        Route::get('/dripfeed/edit/{dripfeed}', 'OpaSocial\DripFeedController@edit');
        Route::post('/dripfeed', 'OpaSocial\DripFeedController@update');
        Route::get('/dripfeed/{df}/details', 'OpaSocial\DripFeedController@details');
        Route::get('/orders-filters/manual', 'OpaSocial\OrderController@indexmanual');
        Route::get('/orders-filters-ajax/manual/data', 'OpaSocial\OrderController@indexDataManual');
        Route::get('/autolike', 'OpaSocial\AutoLikeController@index');
        Route::get('/autolike-index/data', 'OpaSocial\AutoLikeController@indexData');
        Route::get('/autolike/edit/{autolike}', 'OpaSocial\AutoLikeController@edit');
        Route::post('/autolike', 'OpaSocial\AutoLikeController@update');
        Route::get('/autolike/{al}/details', 'OpaSocial\AutoLikeController@details');

        Route::get('/points-history', 'UserController@getRedeemHistory');
        Route::get('/points-history/data', 'UserController@getRedeemHistoryData');
        Route::put('/users/point-funds/{id}', 'UserController@redeemAccept');
        Route::put('/users/point-fundsreject/{id}', 'UserController@redeemReject');
        Route::resource('/orders', 'OpaSocial\OrderController');
        Route::post('/order/{id}/complete', 'OpaSocial\OrderController@completeOrder');
        Route::get('/orders-ajax/data', 'OpaSocial\OrderController@indexData');
        Route::post('/orders-bulk-update', 'OpaSocial\OrderController@bulkUpdate');
        Route::get('/orders-filter/{status}', 'OpaSocial\OrderController@indexFilter');
        Route::get('/orders-filter-ajax/{status}/data', 'OpaSocial\OrderController@indexFilterData');

        Route::get('/subscriptions', 'OpaSocial\SubscriptionController@index');
        Route::get('/subscriptions-index/data', 'OpaSocial\SubscriptionController@indexData');
        Route::get('/subscriptions/{id}/edit', 'OpaSocial\SubscriptionController@edit');
        Route::post('/subscriptions/{id}', 'OpaSocial\SubscriptionController@update');
        Route::put('/subscriptions/{id}/cancel', 'OpaSocial\SubscriptionController@cancel');
        Route::put('/subscriptions/{id}/stop', 'OpaSocial\SubscriptionController@stop');
        Route::get('/subscriptions/{id}/orders', 'OpaSocial\SubscriptionController@orders');
        Route::post('/subscriptions/{id}/order', 'OpaSocial\SubscriptionController@storeOrder');
        Route::get('/subscriptions-filter/{status}', 'OpaSocial\SubscriptionController@indexFilter');
        Route::get('/subscriptions-filter-ajax/{status}/data', 'OpaSocial\SubscriptionController@indexFilterData');

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
        Route::get('/automate/api/addmxz', 'OpaSocial\AutomateController@addApimxz');
        Route::post('/automate/api/addmxz', 'OpaSocial\AutomateController@storeApimxz');
        Route::get('/automate/api/addperfectpanel', 'OpaSocial\AutomateController@addApiperfect');
        Route::post('/automate/api/addperfectpanel', 'OpaSocial\AutomateController@storeApiperfect');
        Route::get('/automate/api-list', 'OpaSocial\AutomateController@listApi');
        Route::get('/automate/send-orders', 'OpaSocial\AutomateController@sendOrdersIndex');
        Route::get('/automate/send-orders-index/data', 'OpaSocial\AutomateController@sendOrdersIndexData');
        Route::post('/automate/send-order-to-api', 'OpaSocial\AutomateController@sendOrderToApi');

        Route::get('/automate/response-logs', 'OpaSocial\AutomateController@getResponseLogsIndex');
        Route::get('/automate/response-logs-index/data', 'OpaSocial\AutomateController@getResponseLogsIndexData');
        Route::get('notification/{id}', 'OpaSocial\OrderController@notification');

        Route::get('/refills/list', 'OpaSocial\RefillRequestController@index');
        Route::get('/refills-ajax/data', 'OpaSocial\RefillRequestController@indexData');
        Route::get('/refills-filter/{status}', 'OpaSocial\RefillRequestController@indexFilter');
        Route::get('/refills-filter-ajax/{status}/data', 'OpaSocial\RefillRequestController@indexFilterData');
        Route::get('/refills/{order}/details', 'OpaSocial\RefillRequestController@details');
        Route::get('/refill/{refill}/{status}', 'OpaSocial\RefillRequestController@changeStatus');

        Route::get('/automate/api/add', 'OpaSocial\AutomateController@addApi');
        Route::post('/automate/api/add', 'OpaSocial\AutomateController@storeApi');
        Route::get('/automate/api/{id}/edit', 'OpaSocial\AutomateController@editApi');
        Route::delete('/automate/api/{id}', 'OpaSocial\AutomateController@deleteApi');
        Route::put('/automate/api/{id}', 'OpaSocial\AutomateController@updateApi');
        Route::post('/automate/api/mapping/{id}', 'OpaSocial\AutomateController@storeMapping');
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
        Route::get('/refill-complete', 'OpaSocial\RefillRequestController@completeStatus');

        Route::get('/automate/get-status', 'OpaSocial\AutomateController@getOrderStatusIndex');
        Route::get('/automate/get-status-index/data', 'OpaSocial\AutomateController@getOrderStatusIndexData');
        Route::post('/automate/get-status-from-api', 'OpaSocial\AutomateController@getOrderStatusFromAPI');
        Route::post('/automate/change-reseller', 'OpaSocial\AutomateController@changeReseller');

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

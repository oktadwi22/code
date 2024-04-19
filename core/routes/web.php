<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/ticketbutnotused', 'supportTicket')->name('index');
    Route::get('newticketbutnotused', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('viewticketbutnotused/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('downloadticketbutnotused/{ticket}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::middleware('auth')->controller('CartController')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::delete('/{id}', 'delete')->name('delete');
    Route::get('/toggle-extended/{id}', 'toggleExtended')->name('extended.toggle');
    Route::get('collections/{id}/add-to-cart', 'CartController@collectionToCart')->name('collections.cart');

    Route::get('/get-cart-list', 'getCartList')->name('getCartList');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/products', 'products')->name('products');
    Route::get('/products/{slug}', 'productDetails')->name('product.details');
    Route::get('/products/{slug}/reviews', 'productReviews')->name('product.reviews');
    Route::get('/products/{slug}/comments', 'productComments')->name('product.comments');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');


    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});


// Route::group(['middleware' => 'auth'], function () {
// });

Route::middleware('web')->as('web3')->prefix('_web3')->group(function () {
    $routes = config('web3.routes', ['signature', 'link', 'login', 'register']);

    if (in_array('signature', $routes)) {
        Route::get('signature', 'Web3LoginController@signature')->name('.signature');
    }

    if (in_array('link', $routes)) {
        Route::post('link', 'Web3LoginController@link')->middleware('auth')->name('.link');
    }

    if (in_array('login', $routes)) {
        Route::post('login', 'Web3LoginController@login')->middleware('guest')->name('.login');
    }

    if (in_array('register', $routes)) {
        Route::post('register', 'Web3LoginController@register')->name('.register');
    }
});


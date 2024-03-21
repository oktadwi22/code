<?php

use Illuminate\Support\Facades\Route;



Route::namespace('User\Auth')->name('user.')->group(function () {

    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});


Route::middleware('auth')->name('user.')->group(function () {
    // authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::namespace('User')->group(function () {

            Route::post('/order', 'OrderController@store')->name('order.store');
            Route::get('/orders', 'OrderController@orderList')->name('order.list');
            Route::get('/orders/{id}/details', 'OrderController@details')->name('order.details');

            Route::middleware('registration.complete')->controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');

                // 2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                // KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                // Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::get('attachment-download/{file_hash}', 'attachmentDownload')->name('attachment.download');
            });

            // Profile setting
            Route::middleware('registration.complete')->controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::get('my-profile', 'myProfile')->name('profile.my');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            // product
            Route::controller('ProductController')->middleware(['registration.complete', 'author'])->name('product.')->prefix('product')->group(function () {
                Route::get('select-category', 'selectCategory')->name('upload.category');
                Route::get('upload', 'upload')->name('upload');
                Route::post('upload/{id?}', 'saveProduct')->name('save');
                Route::get('/{id}/activites', 'productActivities')->name('activites');
                Route::post('/{slug}/activites', 'replyActivity')->name('activites.reply');
                Route::get('edit/{slug}', 'edit')->name('edit');
                Route::post('delete/{id}', 'destroy')->name('delete');
            });

            // author
            Route::middleware('registration.complete')->controller('AuthorController')->name('author.')->group(function () {
                Route::get('author-form', 'authorInfoForm')->name('form');
                Route::post('author-form', 'authorInfoFormSubmit')->name('form.submit');
                Route::post('settings-save', 'saveSettings')->name('settings.save');
                Route::post('follow/{user}', 'follow')->name('follow');

                // only for author
                Route::get('refunds', 'refunds')->name('refunds');
                Route::get('refunds/{id}/activity', 'refundActivity')->name('refunds.activity');
                Route::post('refunds/{id}/activity', 'refundActivityReply')->name('refunds.activity.reply');

                Route::middleware(['author'])->group(function () {
                    Route::get('hidden-items', 'hiddenItems')->name('hidden_items');
                    Route::post('refunds/{id}/accept', 'acceptRefundRequest')->name('refunds.accept');
                    Route::post('refunds/{id}/reject', 'rejectRefundRequest')->name('refunds.reject');
                    Route::get('earning', 'earning')->name('earning');
                    Route::get('reviews', 'reviewList')->name('reviews.index');
                    Route::post('reviews/{productId}/reply/{reviewId}', 'reviewReply')->name('review.reply');
                    Route::get('sales', 'sells')->name('sells');
                    Route::get('comments', 'commentList')->name('comments.index');
                    Route::get('comments/{commentId}/replies', 'repliesList')->name('comments.replies.index');
                    Route::post('comments/replies/{id}/delete', 'deleteReply')->name('comments.replies.delete');
                    Route::post('comments/{id}/delete', 'deleteComment')->name('comments.delete');
                });

                Route::post('comments/{productId}', 'saveComment')->name('comment.store');
                Route::post('reviews/{productId}', 'saveReview')->name('review.store');
                Route::get('download', 'download')->name('download');
                Route::get('collections', 'collections')->name('collections');
                Route::post('collections', 'storeCollection')->name('collections.store');
                Route::post('collections/{id}/update', 'updateCollection')->name('collections.update');
                Route::post('collections/{id}/delete', 'deleteCollection')->name('collections.delete');
                Route::post('collections/products/{id}', 'storeProductsToCollection')->name('collections.products.store');
                Route::get('collections/products/{id}', 'getProductsCollections')->name('collections.products.list');
                Route::get('favorites', 'favorites')->name('favorites');
                Route::post('favorites', 'toggleFavorite')->name('favorites.toggle');
                Route::delete('favorites', 'removeFavorite')->name('favorites.remove');
                Route::get('checkout', 'checkout')->name('checkout');
                Route::post('email/{authorId}', 'sendMailToAuthor')->name('mail');
                Route::get('download-product/{purchaseCode}', 'downloadProduct')->name('product.download');
                Route::post('refund/request/{purchase_code}', 'refundRequest')->name('refund.request');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });
        });

        // Payment
        Route::middleware('registration.complete')
            ->prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
                Route::any('/', 'deposit')->name('index');
                Route::post('insert', 'depositInsert')->name('insert');
                Route::get('confirm', 'depositConfirm')->name('confirm');
                Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
                Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            });
    });
});

Route::controller('User\AuthorController')->name('user.')->prefix('/{username?}')->group(function () {
    Route::get('/', 'showProfile')->name('profile');
    Route::get('collections/{id}', 'collectionDetails')->name('collections.details');
    Route::get('/portfolio', 'portfolio')->name('portfolio');
    Route::get('/followers', 'followers')->name('followers');
    Route::get('/following', 'following')->name('following');
});

<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('reset');
        Route::post('reset', 'sendResetCodeEmail');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware('reviewer')->group(function () {
    Route::controller('ReviewerController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');
    });

    Route::controller('ProductController')->prefix('product')->name('product.')->group(function () {
        Route::post('/{slug}/assign', 'assignProduct')->name('assign');
        Route::get('/{id}/download', 'downloadProduct')->name('download');
        Route::get('/{id}/download-temp', 'downloadProductTemp')->name('download.temp');
        Route::get('/{id}/details', 'details')->name('details');
        Route::post('/{id}/approve', 'approveProduct')->name('approve');
        Route::post('/{id}/reject/{type}', 'rejectProduct')->name('reject')->where(['type' => '[1-5]']);
        Route::get('/awating', 'awatingProducts')->name('awating');
        Route::get('/pending', 'pendingProducts')->name('pending');
        Route::get('/assigned', 'assignedProducts')->name('assigned');
        Route::get('/approved', 'approvedProducts')->name('approved');
        Route::get('/soft-rejected', 'softRejectedProducts')->name('rejected.soft');
        Route::get('/hard-rejected', 'hardRejectedProducts')->name('rejected.hard');
        Route::get('/down', 'downProducts')->name('down');
        Route::get('/permanent-down', 'permanentDownProducts')->name('down.permanent');
        Route::get('/updated', 'updatedProducts')->name('updated');
        Route::post('/update/{id}/approve', 'approveUpdate')->name('update.approve');
        Route::post('/update/{id}/reject/{type}', 'rejectUpdate')->name('update.reject')->where(['type' => '[1-4]']);
        Route::post('/{id}/activities', 'replyActivity')->name('activities.reply');
    });
});

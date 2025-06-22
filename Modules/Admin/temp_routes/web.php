<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\CategoriesController;
use Modules\Admin\Http\Controllers\RolesController;
use Modules\Admin\Http\Controllers\UserManagementController;
use Modules\Admin\Http\Controllers\SmsController;
use Modules\Admin\Http\Controllers\SettingsController;
use Modules\Admin\Http\Controllers\EmailController;


Route::middleware(['auth', 'verified'])->group(function () {
    /* =======  Categories Routes Start  ====== */
    Route::group(['middleware' => ['can:read categories']], function () {
        Route::get('/categories/view', [CategoriesController::class, 'index'])->name('categories-view');
        Route::get('/Categories/fetchList', [CategoriesController::class, 'fetchList'])->name('Categories-fetchList');
    });

    Route::group(['middleware' => ['can:create categories']], function () {
        Route::post('/categories/add', [CategoriesController::class, 'create'])
            ->name('categories-add');

        Route::post('/categories/store', [CategoriesController::class, 'store'])
            ->name('categories-Store');
    });

    Route::group(['middleware' => ['can:update categories']], function () {

        Route::post('/Categories/edit/{id}', [CategoriesController::class, 'edit'])->name('Categories-edit');

        Route::post('/categories/update', [CategoriesController::class, 'update'])
            ->name('categories-update');

        Route::post('/Categories/status/update', [CategoriesController::class, 'categoriesStatusUpdate'])
            ->name('Categories-status-update');
    });

    Route::group(['middleware' => ['can:delete categories']], function () {
        Route::post('/Categories/delete/{id}', [CategoriesController::class, 'delete'])
            ->name('Categories-delete');
    });
    /* =======  Categories Routes End  ====== */



    /* =======  Roles Routes Start  ====== */
    Route::group(['middleware' => ['can:read roles']], function () {
        Route::get('/roles/view', [RolesController::class, 'index'])->name('roles-view');
        Route::get('/roles/fetchList', [RolesController::class, 'fetchList'])->name('roles-fetchList');
    });

    Route::group(['middleware' => ['can:create roles']], function () {
        Route::post('/roles/add', [RolesController::class, 'create'])
            ->name('roles-add');

        Route::post('/roles/store', [RolesController::class, 'store'])
            ->name('roles-store');
    });


    Route::group(['middleware' => ['can:update roles']], function () {

        Route::post('/roles/edit/{id}', [RolesController::class, 'edit'])->name('roles-edit');

        Route::post('/roles/update', [RolesController::class, 'update'])
            ->name('roles-update');

        Route::post('/roles/status/update', [RolesController::class, 'rolesStatusUpdate'])
            ->name('roles-status-update');
    });



    Route::group(['middleware' => ['can:delete roles']], function () {
        Route::post('/roles/delete/{id}', [RolesController::class, 'delete'])
            ->name('Categories-delete');
    });





    Route::get('/roles/select2', [UserManagementController::class, 'select2']);


    /* =======  Roles Routes End  ====== */



    /* =======  User Management Routes Start  ====== */
    Route::group(['middleware' => ['can:read user']], function () {
        Route::get('/user-management/view', [UserManagementController::class, 'index'])->name('user-mangement-view');
        Route::get('/user-mangement/fetchList', [UserManagementController::class, 'fetchList'])->name('user-mangement-fetchList');
    });

    Route::group(['middleware' => ['can:create user']], function () {
        Route::post('/user-mangement/add', [UserManagementController::class, 'create'])
            ->name('user-mangement-add');

        Route::post('/user-management/store', [UserManagementController::class, 'store'])
            ->name('user-management-store');
    });


    Route::post('/user-management/update', [UserManagementController::class, 'update'])
            ->name('user-management-update');

    Route::post('/user-mangement/status/update', [UserManagementController::class, 'userStatusUpdate'])
            ->name('user-management-status-update');

    Route::post('/user-mangement/edit/{id}', [UserManagementController::class, 'edit'])->name('user-mangement-edit');

    Route::get('/roles/select2/{id?}', [UserManagementController::class, 'getRoleById']);








    Route::get('/sms/view', [SmsController::class, 'index'])->name('sms-view');
    Route::get('/sms/fetchList', [SmsController::class, 'fetchList'])->name('sms-fetchList');


    Route::post('/sms/add', [SmsController::class, 'create'])
            ->name('sms-add');

    Route::post('/sms/store', [SmsController::class, 'store'])
    ->name('sms-store');

    Route::post('/sms/send', [SmsController::class, 'sendSms'])
    ->name('sms-send');

    Route::post('/sms/manual/store', [SmsController::class, 'manualStore'])
    ->name('sms-manual-store');


    Route::post('/sms/update/{id}', [SmsController::class, 'update']);








    Route::get('/settings/view', [SettingsController::class, 'index'])->name('settings-view');
    Route::post('/api/notify/store', [SettingsController::class, 'notifyApiStore'])
    ->name('api-notify-store');



    Route::get('/email/view', [EmailController::class, 'index'])->name('email-view');
    Route::get('/email/fetchList', [EmailController::class, 'fetchList'])->name('email-fetchList');

    Route::post('/email/add', [EmailController::class, 'create'])
            ->name('email-add');

    Route::post('/email/store', [EmailController::class, 'store'])
    ->name('email-store');

    Route::post('/email/manual/store', [EmailController::class, 'manualStore'])
    ->name('email-manual-store');


    Route::post('/email/send', [EmailController::class, 'sendEmail'])
    ->name('email-send');

      Route::post('/email/update/{id}', [EmailController::class, 'update']);


 
});

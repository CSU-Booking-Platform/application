<?php

use App\Http\Controllers\BlackoutController;
use App\Http\Controllers\BookingRequestController;
use App\Http\Controllers\BookingReviewController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\RestrictionsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomDateRestrictionsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia\Inertia::render('Dashboard');
    })->name('dashboard');

    /**
     * ADMIN
     */
    Route::name('admin.')->prefix('admin')->group(function () {
        //TODO: Admin index route

        /**
         * USERS
         */
        Route::name('users.')->prefix('users')->group(function() {
            Route::get('/', [UserController::class, 'index'])
                ->name('index');

            Route::post('/', [UserController::class, 'store'])
                ->name('store')
                ->middleware(['permission:users.create']);

            Route::put('/{user}', [UserController::class, 'update'])
                ->name('update')
                ->middleware(['permission:users.update']);

            Route::delete('/{user}', [UserController::class, 'destroy'])
                ->name('destroy')
                ->middleware(['permission:users.delete']);
        });

        /**
         * ROLES
         */
        Route::name('roles.')->prefix('roles')->group(function() {
            Route::get('/', [RoleController::class, 'index'])
                ->name('index');

            Route::post('/', [RoleController::class, 'store'])
                ->name('store')
                ->middleware(['permission:roles.create']);

            Route::put('/{role}', [RoleController::class, 'update'])
                ->name('update')
                ->middleware(['permission:roles.update']);

            Route::delete('/{role}', [RoleController::class, 'destroy'])
                ->name('destroy')
                ->middleware(['permission:roles.delete']);
        });

        /**
         * ROOMS
         */
        Route::name('rooms.')->prefix('rooms')->group(function() {
            Route::get('/', [RoomController::class, 'index'])
                ->name('index');

            Route::post('/', [RoomController::class, 'store'])
                ->name('store')
                ->middleware(['permission:rooms.create']);

            Route::put('/{room}', [RoomController::class, 'update'])
                ->name('update')
                ->middleware(['permission:rooms.update']);

            Route::delete('/{room}', [RoomController::class, 'destroy'])
                ->name('destroy')
                ->middleware(['permission:rooms.delete']);

            Route::put('/{room}/restrictions', [RestrictionsController::class, 'update'])
                ->name('restrictions.update')
                ->middleware('permission:bookings.approve');

            Route::put('/{room}/date-restrictions', RoomDateRestrictionsController::class)
                ->name('date.restrictions.update')
                ->middleware('permission:bookings.approve');

            Route::name('blackouts.')->prefix('{room}/blackouts')->group(function() {
                Route::get('/', [BlackoutController::class, 'index'])
                    ->name('index');

                Route::post('/', [BlackoutController::class, 'store'])
                    ->name('store')
                    ->middleware(['permission:rooms.blackouts.create']);

                Route::put('/{blackout}', [BlackoutController::class, 'update'])
                    ->name('update')
                    ->middleware(['permission:rooms.blackouts.update']);

                Route::delete('/{blackout}', [BlackoutController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware(['permission:rooms.blackouts.delete']);
            });
        });

        /**
         * SETTINGS
         */
        Route::name('settings.')->prefix('settings')->middleware(['permission:settings.edit'])->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('app_logo', [SettingsController::class, 'storeAppLogo'])->name('app.logo');
            Route::post('app_name', [SettingsController::class, 'storeAppName'])->name('app.name');
        });
    });

    /**
     * BOOKINGS
     */
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::resource('', BookingRequestController::class)->except('show')->parameters(['' => 'booking']);
        Route::post('create', [BookingRequestController::class, 'createInit'])->name('createInit');
        Route::get('list', [BookingRequestController::class, 'list'])->name('list');
        Route::get('download/{folder}', [BookingRequestController::class, 'downloadReferenceFiles'])->name('download');

        Route::name('reviews.')->middleware('permission:bookings.approve')->group(function () {
            Route::get('review', [BookingReviewController::class, 'index'])->name('index');
            Route::get('{booking}/review', [BookingReviewController::class, 'show'])->name('show');
            Route::post('{booking}/review', [BookingReviewController::class, 'review'])->name('update');
        });
    });

    Route::resource('reservation', ReservationsController::class);

});

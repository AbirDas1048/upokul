<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PublishController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
| Every authenticated route is also protected by a "can:module.action" permission (see
| config/permissions.php); the master admin passes them all. Content types are dynamic
| (/admin/content/{type}) and check their own "<type>.<action>" permission in the controller.
*/

Route::prefix('admin')->name('admin.')->middleware('no-store')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->middleware('throttle:admin-login')->name('login.submit');
    });

    Route::middleware(['auth', 'admin.active'])->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->middleware('can:dashboard.view')->name('dashboard');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        // Contact messages
        Route::get('contacts', [ContactController::class, 'index'])->middleware('can:contacts.view')->name('contacts.index');
        Route::get('contacts/{contact}', [ContactController::class, 'show'])->middleware('can:contacts.view')->name('contacts.show');
        Route::post('contacts/{contact}/toggle-read', [ContactController::class, 'toggleRead'])->middleware('can:contacts.update')->name('contacts.toggle-read');
        Route::post('contacts/{contact}/resend', [ContactController::class, 'resend'])->middleware('can:contacts.update')->name('contacts.resend');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->middleware('can:contacts.delete')->name('contacts.destroy');

        // Page sections (texts + on/off)
        Route::get('sections', [SectionController::class, 'index'])->middleware('can:sections.view')->name('sections.index');
        Route::get('sections/{section}', [SectionController::class, 'edit'])->middleware('can:sections.view')->name('sections.edit');
        Route::put('sections/{section}', [SectionController::class, 'update'])->middleware('can:sections.update')->name('sections.update');

        // Repeatable content (services, journeys, FAQs...)
        Route::prefix('content/{type}')->name('content.')->whereIn('type', array_keys(config('content_types')))->group(function () {
            Route::get('/', [ContentController::class, 'index'])->name('index');
            Route::get('create', [ContentController::class, 'create'])->name('create');
            Route::post('/', [ContentController::class, 'store'])->name('store');
            Route::get('{id}/edit', [ContentController::class, 'edit'])->whereNumber('id')->name('edit');
            Route::put('{id}', [ContentController::class, 'update'])->whereNumber('id')->name('update');
            Route::delete('{id}', [ContentController::class, 'destroy'])->whereNumber('id')->name('destroy');
        });

        // Draft → live
        Route::post('publish', [PublishController::class, 'store'])->middleware('can:publish.update')->name('publish');

        // Configuration
        Route::get('appearance', [AppearanceController::class, 'index'])->middleware('can:appearance.view')->name('appearance.index');
        Route::put('appearance', [AppearanceController::class, 'update'])->middleware('can:appearance.update')->name('appearance.update');

        Route::get('settings/{group?}', [SettingsController::class, 'edit'])->whereIn('group', ['general', 'contact', 'branding', 'seo', 'mail'])->name('settings.edit');
        Route::put('settings/{group}', [SettingsController::class, 'update'])->whereIn('group', ['general', 'contact', 'branding', 'seo', 'mail'])->name('settings.update');
        Route::post('settings/mail/test', [SettingsController::class, 'testMail'])->middleware('can:mail.update')->name('settings.mail-test');

        // Administration
        Route::resource('users', UserController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');

        Route::get('activity', [ActivityController::class, 'index'])->middleware('can:activity.view')->name('activity.index');
        Route::delete('activity', [ActivityController::class, 'clear'])->middleware('can:activity.delete')->name('activity.clear');
    });
});

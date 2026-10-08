<?php

declare(strict_types=1);

use FireflyIII\Api\V1\Controllers\Models\TransactionTemplate\ListController;
use FireflyIII\Http\Controllers\TransactionTemplate\TransactionTemplateController;
use Illuminate\Support\Facades\Route;

// Kept separate to facilitate upstream merges to fork given the feature likely won't be upstreamed.

// Web routes (the admin UI).
Route::group(
    [
        'middleware' => ['web', 'user-full-auth'],
        'prefix'     => 'transaction-templates',
        'as'         => 'transaction-templates.',
    ],
    static function (): void {
        Route::get('', [TransactionTemplateController::class, 'index'])->name('index');
        Route::get('create', [TransactionTemplateController::class, 'create'])->name('create');
        Route::post('store', [TransactionTemplateController::class, 'store'])->name('store');
        Route::get('edit/{id}', [TransactionTemplateController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [TransactionTemplateController::class, 'update'])->name('update');
        Route::get('delete/{id}', [TransactionTemplateController::class, 'delete'])->name('delete');
        Route::post('destroy/{id}', [TransactionTemplateController::class, 'destroy'])->name('destroy');
    }
);

// API v1 route. Declared here (instead of in routes/api.php) so the fork's
// feature stays in one file; same prefix, naming and middleware as the
// groups in routes/api.php.
Route::group(
    [
        'middleware' => ['api', 'user-full-auth'],
        'prefix'     => 'api/v1',
        'as'         => 'api.v1.transaction-templates.',
    ],
    static function (): void {
        Route::get('transaction-templates', [ListController::class, 'index'])->name('index');
    }
);

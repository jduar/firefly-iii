<?php

declare(strict_types=1);

use FireflyIII\Http\Controllers\TransactionTemplate\TransactionTemplateController;
use Illuminate\Support\Facades\Route;

// Kept separate to facilitate upstream merges to fork given the feature likely won't be upstreamed.

Route::group(
    [
        'middleware' => 'user-full-auth',
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

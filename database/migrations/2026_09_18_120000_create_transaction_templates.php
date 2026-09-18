<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaction_templates', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('user_id', false, true);

            $table->string('name', 255);
            $table->string('transaction_description', 1000)->nullable();

            $table->integer('source_account_id', false, true)->nullable();
            $table->integer('destination_account_id', false, true)->nullable();
            $table->integer('budget_id', false, true)->nullable();
            $table->integer('category_id', false, true)->nullable();

            $table->text('tags')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('source_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('destination_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('budget_id')->references('id')->on('budgets')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_templates');
    }
};

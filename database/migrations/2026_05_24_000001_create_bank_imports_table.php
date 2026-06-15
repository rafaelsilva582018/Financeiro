<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('entry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();

            $table->string('source')->default('manual');
            $table->string('external_id')->nullable();
            $table->enum('type', ['income', 'expense']);
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('occurred_at');
            $table->enum('status', ['pending', 'linked', 'created', 'ignored'])->default('pending');
            $table->foreignId('suggested_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('suggested_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_imports');
    }
};

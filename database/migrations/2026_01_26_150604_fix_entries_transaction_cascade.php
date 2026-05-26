<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {

            // Remove a foreign key atual
            $table->dropForeign(['transaction_id']);

            // Cria novamente com ON DELETE CASCADE
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {

            $table->dropForeign(['transaction_id']);

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'start_date', 'id'], 'transactions_user_start_id_index');
            $table->index(['user_id', 'type'], 'transactions_user_type_index');
            $table->index(['user_id', 'is_fixed'], 'transactions_user_fixed_index');
            $table->index(['user_id', 'credit_card_id'], 'transactions_user_card_index');
        });

        Schema::table('entries', function (Blueprint $table) {
            $table->index(['user_id', 'reference_date', 'status'], 'entries_user_reference_status_index');
            $table->index(['transaction_id', 'reference_date'], 'entries_transaction_reference_index');
            $table->index(['user_id', 'due_date'], 'entries_user_due_index');
            $table->index(['user_id', 'credit_card_id', 'reference_date'], 'entries_user_card_reference_index');
            $table->index(['user_id', 'account_id', 'reference_date'], 'entries_user_account_reference_index');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropIndex('entries_user_reference_status_index');
            $table->dropIndex('entries_transaction_reference_index');
            $table->dropIndex('entries_user_due_index');
            $table->dropIndex('entries_user_card_reference_index');
            $table->dropIndex('entries_user_account_reference_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_start_id_index');
            $table->dropIndex('transactions_user_type_index');
            $table->dropIndex('transactions_user_fixed_index');
            $table->dropIndex('transactions_user_card_index');
        });
    }
};

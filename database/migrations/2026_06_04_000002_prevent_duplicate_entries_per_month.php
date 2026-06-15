<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateIds = DB::table('entries')
            ->select('id')
            ->whereIn('id', function ($query) {
                $query
                    ->select('duplicate_entries.id')
                    ->from('entries as duplicate_entries')
                    ->join('entries as first_entries', function ($join) {
                        $join
                            ->on('first_entries.transaction_id', '=', 'duplicate_entries.transaction_id')
                            ->on('first_entries.reference_date', '=', 'duplicate_entries.reference_date')
                            ->whereColumn('first_entries.id', '<', 'duplicate_entries.id');
                    });
            })
            ->pluck('id');

        if ($duplicateIds->isNotEmpty()) {
            DB::table('entries')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('entries', function (Blueprint $table) {
            $table->unique(['transaction_id', 'reference_date'], 'entries_transaction_reference_unique');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropUnique('entries_transaction_reference_unique');
        });
    }
};

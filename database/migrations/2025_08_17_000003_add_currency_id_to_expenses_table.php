<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('expenses') && !Schema::hasColumn('expenses', 'currency_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->foreignId('currency_id')->nullable()->after('account_id')->constrained('currencies')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'currency_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropConstrainedForeignId('currency_id');
            });
        }
    }
};

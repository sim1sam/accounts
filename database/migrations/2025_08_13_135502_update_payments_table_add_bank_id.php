<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'bank_id')) {
                $table->foreignId('bank_id')->constrained()->onDelete('cascade');
            }
            if (Schema::hasColumn('payments', 'account_no')) {
                $table->dropColumn('account_no'); // Remove the old account_no column
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'bank_id')) {
                $table->dropForeign(['bank_id']);
                $table->dropColumn('bank_id');
            }
            if (!Schema::hasColumn('payments', 'account_no')) {
                $table->string('account_no');
            }
        });
    }
};

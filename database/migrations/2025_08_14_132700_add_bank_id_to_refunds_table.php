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
        Schema::table('refunds', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->after('customer_id')->constrained();
            $table->dropColumn('account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->string('account')->nullable()->after('refund_date');
            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update currencies table boolean columns
        Schema::table('currencies', function (Blueprint $table) {
            // Drop the existing columns
            $table->dropColumn('is_default');
            $table->dropColumn('is_active');
        });
        
        Schema::table('currencies', function (Blueprint $table) {
            // Recreate with explicit tinyInteger(1)
            $table->tinyInteger('is_default')->default(0)->after('conversion_rate');
            $table->tinyInteger('is_active')->default(1)->after('is_default');
        });
        
        // Update accounts table boolean columns
        Schema::table('accounts', function (Blueprint $table) {
            // Drop the existing column
            $table->dropColumn('is_active');
        });
        
        Schema::table('accounts', function (Blueprint $table) {
            // Recreate with explicit tinyInteger(1)
            $table->tinyInteger('is_active')->default(1)->after('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert currencies table changes
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropColumn('is_active');
        });
        
        Schema::table('currencies', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('conversion_rate');
            $table->boolean('is_active')->default(true)->after('is_default');
        });
        
        // Revert accounts table changes
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
        
        Schema::table('accounts', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('currency_id');
        });
    }
};

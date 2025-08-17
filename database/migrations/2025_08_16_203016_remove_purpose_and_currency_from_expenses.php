<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePurposeAndCurrencyFromExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('expenses')) {
            if (Schema::hasColumn('expenses', 'purpose')) {
                Schema::table('expenses', function (Blueprint $table) {
                    $table->dropColumn('purpose');
                });
            }
            if (Schema::hasColumn('expenses', 'currency_id')) {
                Schema::table('expenses', function (Blueprint $table) {
                    // Drop FK if exists, then drop column
                    try { $table->dropForeign(['currency_id']); } catch (\Throwable $e) { /* ignore if not exists */ }
                    $table->dropColumn('currency_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('expenses')) {
            if (!Schema::hasColumn('expenses', 'purpose')) {
                Schema::table('expenses', function (Blueprint $table) {
                    $table->string('purpose')->after('id');
                });
            }
            if (!Schema::hasColumn('expenses', 'currency_id')) {
                Schema::table('expenses', function (Blueprint $table) {
                    $table->foreignId('currency_id')->after('amount')->constrained();
                });
            }
        }
    }
}

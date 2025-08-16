<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyIdToBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('branch')->constrained();
            $table->decimal('amount_in_bdt', 15, 2)->default(0)->after('current_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'amount_in_bdt']);
        });
    }
}

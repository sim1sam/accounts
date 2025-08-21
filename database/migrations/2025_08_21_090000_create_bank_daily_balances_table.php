<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_daily_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('physical_amount', 18, 2); // in bank currency
            $table->decimal('physical_amount_bdt', 18, 2); // converted to BDT for comparison
            $table->decimal('system_amount_bdt', 18, 2); // snapshot of system BDT at entry time
            $table->decimal('difference_bdt', 18, 2); // physical - system (BDT)
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['bank_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_daily_balances');
    }
};

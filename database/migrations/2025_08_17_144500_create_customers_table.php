<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable()->unique();
                $table->string('email')->nullable()->unique();
                $table->string('address')->nullable();
                $table->string('country')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0);
                $table->foreignId('currency_id')->nullable()->constrained();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::dropIfExists('customers');
        }
    }
};

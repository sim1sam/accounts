<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cancellations', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('customer_id')->constrained('staff')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cancellations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('staff_id');
        });
    }
};

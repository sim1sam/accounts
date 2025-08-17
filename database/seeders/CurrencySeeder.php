<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Idempotent: upsert currencies by code
        Currency::updateOrCreate(
            ['code' => 'BDT'],
            [
                'name' => 'Bangladeshi Taka',
                'symbol' => '৳',
                'conversion_rate' => 1.00000,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        Currency::updateOrCreate(
            ['code' => 'INR'],
            [
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'conversion_rate' => 0.87500,
                'is_default' => false,
                'is_active' => true,
            ]
        );

        Currency::updateOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'US Dollar',
                'symbol' => '$',
                'conversion_rate' => 110.00000,
                'is_default' => false,
                'is_active' => true,
            ]
        );

        // Ensure only BDT is default
        Currency::where('code', '!=', 'BDT')->update(['is_default' => false]);
    }
}

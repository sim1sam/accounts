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
        // Create default currencies
        Currency::create([
            'name' => 'Bangladeshi Taka',
            'code' => 'BDT',
            'symbol' => '৳',
            'conversion_rate' => 1.00000, // Base currency
            'is_default' => true,
            'is_active' => true
        ]);

        Currency::create([
            'name' => 'Indian Rupee',
            'code' => 'INR',
            'symbol' => '₹',
            'conversion_rate' => 0.87500, // 1 INR = 0.875 BDT (example rate)
            'is_default' => false,
            'is_active' => true
        ]);

        Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'conversion_rate' => 110.00000, // 1 USD = 110 BDT (example rate)
            'is_default' => false,
            'is_active' => true
        ]);
    }
}

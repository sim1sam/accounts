<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the categories we want to assign to existing accounts
        $categories = [
            'Purchase',
            'Overhead',
            'Tangible Asset',
            'Intangible Asset',
            'Personal Expense',
            'Tax'
        ];
        
        // Get all accounts
        $accounts = Account::all();
        
        // Assign random categories to existing accounts
        foreach ($accounts as $account) {
            // Skip if account already has a category
            if (!empty($account->category)) {
                continue;
            }
            
            // Assign a random category
            $randomCategory = $categories[array_rand($categories)];
            $account->category = $randomCategory;
            $account->save();
        }
        
        $this->command->info('Account categories have been seeded successfully!');
    }
}

@php
/**
 * Helper file for personal expense calculations
 * This file is included in the profit_loss index view
 */

/**
 * Get the paid amount for a personal expense account
 * Uses the personalExpensePaidAmounts array passed from the controller
 * 
 * @param App\Models\Account $account
 * @param array $personalExpensePaidAmounts
 * @param float $inrRate
 * @return array
 */
function getPersonalExpensePaidAmount($account, $personalExpensePaidAmounts, $inrRate) {
    // Get the pre-calculated paid amount for this account
    if (isset($personalExpensePaidAmounts[$account->id])) {
        return $personalExpensePaidAmounts[$account->id];
    }
    
    // Fallback to zero if no paid expenses found
    return [
        'bdt' => 0,
        'inr' => 0
    ];
}

/**
 * Calculate the total of all paid personal expenses
 * 
 * @param Collection $accounts
 * @param array $personalExpensePaidAmounts
 * @param float $inrRate
 * @return array
 */
function getTotalPaidPersonalExpenses($accounts, $personalExpensePaidAmounts, $inrRate) {
    $totalBdt = 0;
    
    foreach ($accounts as $account) {
        $paidAmount = getPersonalExpensePaidAmount($account, $personalExpensePaidAmounts, $inrRate);
        $totalBdt += $paidAmount['bdt'];
    }
    
    return [
        'bdt' => $totalBdt,
        'inr' => $totalBdt / $inrRate
    ];
}
@endphp

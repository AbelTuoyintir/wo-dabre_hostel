<?php

return [
    // Service charge breakdown (percentages as decimals — divide percentage by 100)
    'paystack_fee_rate' => env('PAYSTACK_FEE_RATE', 0.0195),          // 1.95% — Paystack processing fee
    'platform_fee_rate' => env('PLATFORM_FEE_RATE', 0.028),           // 2.80% — Platform fee (Maintenance & Security 0.80% + Platform profit 2.00%)
    'banking_charge_rate' => env('BANKING_CHARGE_RATE', 0.0035),      // 0.35% — Banking charge

    // Total service charge rate (sum of all above)
    'total_service_charge_rate' => env('TOTAL_SERVICE_CHARGE_RATE', 0.051), // 5.10% — Total Service Charge

    // Flat fees in GHS
    'agent_fee' => env('PAYMENTS_AGENT_FEE', 150),

    // Student-specific additional percentage fee (used in student flow)
    'student_fee_percentage' => env('PAYMENTS_STUDENT_FEE_PERCENTAGE', 0.02),
];


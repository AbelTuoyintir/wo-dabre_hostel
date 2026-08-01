<?php

return [
    // ============================================================
    // Paystack Split Payment Fee Structure
    // ============================================================
    // Student pays: room_cost + platform_fee + paystack_buffer + banking_charge
    //   => total = room_cost × (1 + 0.028 + 0.0197 + 0.0035) = room_cost × 1.0512
    //
    // Breakdown:
    //   - Platform Fee (2.80%):   Maintenance & Security Reserve (0.80%) + Platform Profit (2.00%)
    //                              → retained by the main platform account via transaction_charge
    //   - Paystack Buffer (1.97%): Added so SRC receives full room cost after Paystack deducts ~1.95%
    //   - Banking Charge (0.35%):  Banking/operational buffer for SRC
    //
    // SRC Subaccount receives: room_cost + paystack_buffer + banking_charge
    // Paystack automatically deducts its ~1.95% processing fee from the subaccount settlement.
    // ============================================================

    // Platform fee rate — retained by the platform (Maintenance 0.80% + Profit 2.00%)
    'platform_fee_rate' => env('PLATFORM_FEE_RATE', 0.028),

    // Paystack processing fee buffer — added on top so SRC gets full room cost after Paystack deduction
    'paystack_buffer_rate' => env('PAYSTACK_BUFFER_RATE', 0.0197),

    // Banking charge rate — operational buffer for SRC
    'banking_charge_rate' => env('BANKING_CHARGE_RATE', 0.0035),

    // Total surcharge rate applied on top of room_cost (sum of all above)
    'total_surcharge_rate' => env('TOTAL_SURCHARGE_RATE', 0.0512),

    // Flat fees in GHS
    'agent_fee' => env('PAYMENTS_AGENT_FEE', 150),

    // Student-specific additional percentage fee (used in student flow)
    'student_fee_percentage' => env('PAYMENTS_STUDENT_FEE_PERCENTAGE', 0.02),
];

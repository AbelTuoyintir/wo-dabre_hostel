<?php

return [
    // Flat fees in GHS
    'agent_fee' => env('PAYMENTS_AGENT_FEE', 150),
    'system_charge' => env('PAYMENTS_SYSTEM_CHARGE', 20),

    // Paystack/service percentage applied to subtotal (e.g. 0.0195)
    'paystack_fee_multiplier' => env('PAYSTACK_FEE_MULTIPLIER', 0.0195),

    // Student-specific additional percentage fee (used in student flow)
    'student_fee_percentage' => env('PAYMENTS_STUDENT_FEE_PERCENTAGE', 0.02),
];

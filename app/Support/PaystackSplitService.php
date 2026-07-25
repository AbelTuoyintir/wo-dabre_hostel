<?php

namespace App\Support;

use App\Models\Hostel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PaystackSplitService
 *
 * Handles Paystack subaccount creation, verification, and transaction split operations
 * for the hostel booking marketplace.
 *
 * Business Logic:
 * - Each SRC/hostel organization gets a Paystack subaccount for settlement
 * - Subaccount receives: room_cost + paystack_buffer + banking_charge
 * - Platform retains: platform_fee (2.80%) via transaction_charge parameter
 * - Paystack automatically deducts its processing fee (~1.95%) from subaccount settlement
 */
class PaystackSplitService
{
    /**
     * Paystack API base URL
     */
    protected string $baseUrl;

    /**
     * Paystack secret key
     */
    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = Config::get('paystack.secretKey');
        $this->baseUrl = Config::get('paystack.paymentUrl');
    }

    /**
     * Get HTTP headers with authorization
     */
    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Create a Paystack subaccount for a hostel/SRC.
     *
     * @param Hostel $hostel
     * @param array  $bankData  Must contain: business_name, bank_code, account_number, account_name
     * @return array  Response from Paystack API
     */
    public function createSubaccount(Hostel $hostel, array $bankData): array
    {
        $payload = [
            'business_name' => $bankData['business_name'] ?? $hostel->name . ' (SRC)',
            'settlement_bank' => $bankData['bank_code'],
            'account_number' => $bankData['account_number'],
            'primary_contact_email' => $hostel->email ?? '',
            'primary_contact_name' => $bankData['account_name'] ?? '',
            'percentage_charge' => 0, // We use transaction_charge on the transaction, not percentage
            'metadata' => [
                'hostel_id' => $hostel->id,
                'hostel_name' => $hostel->name,
                'source' => 'wo-dabre_split_payment',
            ],
        ];

        Log::info('PaystackSplitService: Creating subaccount', [
            'hostel_id' => $hostel->id,
            'business_name' => $payload['business_name'],
            'bank_code' => $bankData['bank_code'],
            'account_number' => substr($bankData['account_number'], -4),
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/subaccount', $payload);

        $result = $response->json();

        if (!$response->successful() || !($result['status'] ?? false)) {
            Log::error('PaystackSplitService: Failed to create subaccount', [
                'hostel_id' => $hostel->id,
                'response' => $result,
                'status_code' => $response->status(),
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to create subaccount',
                'data' => $result,
            ];
        }

        Log::info('PaystackSplitService: Subaccount created successfully', [
            'hostel_id' => $hostel->id,
            'subaccount_code' => $result['data']['subaccount_code'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Subaccount created successfully',
            'data' => $result['data'] ?? $result,
        ];
    }

    /**
     * Update an existing subaccount for a hostel.
     *
     * @param Hostel $hostel
     * @param array  $bankData  Optional fields to update
     * @return array
     */
    public function updateSubaccount(Hostel $hostel, array $bankData): array
    {
        $subaccountCode = $hostel->subaccount_code;

        if (!$subaccountCode) {
            return [
                'success' => false,
                'message' => 'No subaccount code found for this hostel',
            ];
        }

        $payload = [];

        if (isset($bankData['bank_code'])) {
            $payload['settlement_bank'] = $bankData['bank_code'];
        }
        if (isset($bankData['account_number'])) {
            $payload['account_number'] = $bankData['account_number'];
        }
        if (isset($bankData['account_name'])) {
            $payload['primary_contact_name'] = $bankData['account_name'];
        }
        if (isset($bankData['business_name'])) {
            $payload['business_name'] = $bankData['business_name'];
        }

        Log::info('PaystackSplitService: Updating subaccount', [
            'hostel_id' => $hostel->id,
            'subaccount_code' => $subaccountCode,
        ]);

        $response = Http::withHeaders($this->headers())
            ->put($this->baseUrl . '/subaccount/' . $subaccountCode, $payload);

        $result = $response->json();

        if (!$response->successful() || !($result['status'] ?? false)) {
            Log::error('PaystackSplitService: Failed to update subaccount', [
                'hostel_id' => $hostel->id,
                'response' => $result,
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to update subaccount',
                'data' => $result,
            ];
        }

        return [
            'success' => true,
            'message' => 'Subaccount updated successfully',
            'data' => $result['data'] ?? $result,
        ];
    }

    /**
     * Fetch subaccount details from Paystack.
     *
     * @param string $subaccountCode
     * @return array
     */
    public function fetchSubaccount(string $subaccountCode): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/subaccount/' . $subaccountCode);

        $result = $response->json();

        if (!$response->successful() || !($result['status'] ?? false)) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to fetch subaccount',
                'data' => $result,
            ];
        }

        return [
            'success' => true,
            'message' => 'Subaccount fetched successfully',
            'data' => $result['data'] ?? $result,
        ];
    }

    /**
     * Verify bank account details with Paystack.
     *
     * @param string $accountNumber
     * @param string $bankCode
     * @return array
     */
    public function verifyBankAccount(string $accountNumber, string $bankCode): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/bank/resolve', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

        $result = $response->json();

        if (!$response->successful() || !($result['status'] ?? false)) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to verify bank account',
                'data' => $result,
            ];
        }

        return [
            'success' => true,
            'message' => 'Bank account verified successfully',
            'data' => $result['data'] ?? $result,
        ];
    }

    /**
     * Get list of supported banks from Paystack.
     *
     * @param string $country  Country code (e.g., 'ghana')
     * @return array
     */
    public function getBanks(string $country = 'ghana'): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/bank', [
                'country' => $country,
                'use_cursor' => false,
                'perPage' => 100,
            ]);

        $result = $response->json();

        if (!$response->successful() || !($result['status'] ?? false)) {
            Log::error('PaystackSplitService: Failed to fetch banks', [
                'country' => $country,
                'response' => $result,
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to fetch banks',
                'data' => [],
            ];
        }

        return [
            'success' => true,
            'message' => 'Banks fetched successfully',
            'data' => $result['data'] ?? [],
        ];
    }

    /**
     * Calculate the total amount a student should pay for a booking.
     *
     * Total = room_cost × (1 + platform_fee_rate + paystack_buffer_rate + banking_charge_rate)
     *
     * @param float $roomCost
     * @return array  ['total' => float, 'platform_fee' => float, 'paystack_buffer' => float, 'banking_charge' => float]
     */
    public function calculateTotal(float $roomCost): array
    {
        $platformFeeRate = Config::get('payments.platform_fee_rate', 0.028);
        $paystackBufferRate = Config::get('payments.paystack_buffer_rate', 0.0197);
        $bankingChargeRate = Config::get('payments.banking_charge_rate', 0.0035);

        $platformFee = round($roomCost * $platformFeeRate, 2);
        $paystackBuffer = round($roomCost * $paystackBufferRate, 2);
        $bankingCharge = round($roomCost * $bankingChargeRate, 2);
        $total = round($roomCost + $platformFee + $paystackBuffer + $bankingCharge, 2);

        return [
            'total' => $total,
            'room_cost' => $roomCost,
            'platform_fee' => $platformFee,
            'paystack_buffer' => $paystackBuffer,
            'banking_charge' => $bankingCharge,
        ];
    }

    /**
     * Get the transaction charge amount (platform fee) for Paystack split.
     *
     * This is the amount the platform retains from the transaction.
     * Paystack will settle the remaining (room_cost + paystack_buffer + banking_charge) to the subaccount.
     *
     * @param float $roomCost
     * @return int  Amount in pesewas (GHS × 100)
     */
    public function getTransactionChargeInPesewas(float $roomCost): int
    {
        $platformFeeRate = Config::get('payments.platform_fee_rate', 0.028);
        return (int) round($roomCost * $platformFeeRate * 100);
    }
}


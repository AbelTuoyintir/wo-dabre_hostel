<?php
// app/Console/Commands/ProcessAgentWithdrawals.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgentWithdrawal;
use App\Services\PaymentService;

class ProcessAgentWithdrawals extends Command
{
    protected $signature = 'agent:process-withdrawals';
    protected $description = 'Process pending agent withdrawal requests';

    public function handle(PaymentService $paymentService)
    {
        $pendingWithdrawals = AgentWithdrawal::where('status', 'pending')
            ->whereDate('created_at', '<=', now()->subDays(2))
            ->get();

        foreach ($pendingWithdrawals as $withdrawal) {
            try {
                $result = $paymentService->processWithdrawal($withdrawal);
                
                $withdrawal->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'processed_by' => 1 // System admin
                ]);
                
                $this->info("Processed withdrawal #{$withdrawal->id} for ₵{$withdrawal->amount}");
            } catch (\Exception $e) {
                $this->error("Failed to process withdrawal #{$withdrawal->id}: {$e->getMessage()}");
            }
        }
    }
}
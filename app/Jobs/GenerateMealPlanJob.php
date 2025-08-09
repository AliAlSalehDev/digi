<?php

namespace App\Jobs;

use App\Models\MealSession;
use App\Services\MealGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMealPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 1800; // 30 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(MealSession $session)
    {
        $this->session = $session;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info("Starting meal generation job for session: {$this->session->id}");

            $mealService = new MealGenerationService();
            $mealService->processMealGeneration($this->session);

            Log::info("Completed meal generation job for session: {$this->session->id}");
        } catch (\Exception $e) {
            Log::error("Meal generation job failed for session {$this->session->id}: " . $e->getMessage());
            
            // Update session status to failed
            $this->session->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            // Rethrow to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Meal generation job permanently failed for session {$this->session->id}: " . $exception->getMessage());
        
        // Update session status
        $this->session->update([
            'status' => 'failed',
            'error_message' => 'Job failed after multiple attempts: ' . $exception->getMessage()
        ]);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff()
    {
        return [60, 120, 300]; // 1 minute, 2 minutes, 5 minutes
    }
}
<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $scheduleData;

    /**
     * Create a new job instance.
     *
     * @param array $scheduleData
     */
    public function __construct(array $scheduleData)
    {
        $this->scheduleData = $scheduleData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // create a new schedule
            // Log the data
            // and the queue name
            Log::info('Processing schedule data', $this->scheduleData);

            Schedule::create($this->scheduleData);


        } catch (\Exception $e) {
            Log::critical('Error handling user deletion.', $this->scheduleData);
        }
    }
}

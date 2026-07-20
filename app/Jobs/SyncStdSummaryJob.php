<?php

namespace App\Jobs;

use App\Services\StdSummaryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncStdSummaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        Log::info("Queue Sync STD START");

        StdSummaryService::sync();

        Log::info("Queue Sync STD FINISH");
    }
}
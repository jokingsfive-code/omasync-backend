<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CalendarSource;
use App\Http\Controllers\Api\CalendarSourceController;

class SyncCalendars extends Command
{
    protected $signature = 'omasync:sync-calendars';

    protected $description = 'Sync all active iCal sources';

    public function handle(): int
    {
        $sources = CalendarSource::where('is_active', true)->get();

        if ($sources->isEmpty()) {
            $this->warn('No active calendar sources found.');
            return self::SUCCESS;
        }

        foreach ($sources as $source) {
            try {
                app(CalendarSourceController::class)->syncNow($source->id);

                $this->info("Synced: {$source->channel} | Source ID: {$source->id}");
            } catch (\Throwable $e) {
                $this->error("Failed: {$source->channel} | Source ID: {$source->id}");
                $this->error($e->getMessage());
            }
        }

        $this->info('All active calendars synced.');

        return self::SUCCESS;
    }
}
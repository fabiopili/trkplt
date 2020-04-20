<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

use App\TrackerRadarDomains;

class UpdateTrackerRadar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateTrackerRadar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load DuckDuckGo Tracker Radar from storage and update the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating DuckDuckGo Tracker Radar');

        // Get a list of all files on each relevant subdirectory
        $domains = Storage::disk('tracker-radar')->files('domains');

        // Update or insert each domain
        foreach ($domains as $key => $file) {

            $raw = Storage::disk('tracker-radar')->get($file);
            $data = json_decode($raw, true);

            TrackerRadarDomains::updateOrInsert(
                ['domain' => $data['domain']],
                [
                    'updated_at' => \Carbon\Carbon::now(),
                    'data' => $raw,
                ]
            );
        }

        $this->info('Finished updating ' . count($domains) . ' domains.');

    }
}

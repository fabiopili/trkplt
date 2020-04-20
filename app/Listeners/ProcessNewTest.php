<?php

namespace App\Listeners;

use App\Events\NewTest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;

use App\Services\TestService;

class ProcessNewTest implements ShouldQueue
{

    public $queue = 'tests';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  NewTest  $event
     */
    public function handle(NewTest $event)
    {

        // Log test start
        Log::info('Started test id ' . $event->test->id . ' for URL ' . $event->test->url);

        // Base data coming from the event dispatcher
        $testService = new TestService($event->test);

        // Log test end
        Log::info('Completed test id ' . $event->test->id . ' for URL ' . $event->test->url);

    }

}

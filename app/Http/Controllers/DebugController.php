<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use GuzzleHttp\Client;

use App\BlockList;
use App\Hostname;
use App\Test;

use App\Services\TestService;
use App\Interfaces\TestInterface;

class DebugController extends BaseController
{

	public function __construct(TestInterface $tests)
	{

        // Main Test object
        $this->tests = $tests;

	}

    public function handle()
    {


    	$blocklist = BlockList::find(1);
    	dump($blocklist->hostnames);


    	$hostname = Hostname::find(1);
    	dump($hostname->blockLists);


    	exit();

    	// Create test entry on the database
		$newTest = $this->tests->create([
            'ip' => '2804:14c:658b:455d:656b:eb97:157e:697a',
            'url' => 'https://pili.com.br',
        ]);

        // Base data coming from the event dispatcher
        $testService = new TestService($newTest);

        dd($testService);

    	exit();


    	$url = 'aaaa.gooo.com.br';

		$manager = new \Pdp\Manager(new \Pdp\Cache(), new \Pdp\CurlHttpClient());
    	$rules = $manager->getRules();
		$domain = $rules->resolve('asdsad.x.asdasd.asdasdasd.com.ar');

		dd($domain->getRegistrableDomain());

    	exit();
    	$domain = Hostname::where('id', 1)->first();

    	dd($domain->info);

    	exit();

		$flushCache = false;
		$cacheTime = 86400;
    	$url = 'https://flexnburn.com';

		// Response array and base data structure
    	$response = [];

    	// Purge cache entry for easier debugging
    	if($flushCache){
    		Cache::flush();
    	}

		$browserResponse = Cache::remember('browser_' . sha1($url), $cacheTime, function() use ($url) {

			return json_decode(
				$this->client->request('POST', 'trkpltt_node:4444/', [
					'json' => ['url' => $url]
				])->getBody()->getContents()
			, true);

		});

		// Make sure I have data
		if(!empty($browserResponse['data']['requests'])){

			// Parse all requests to a new array with only the unique domains
			$uniqueDomains = array_map(function($request){
			    return parse_url($request['request_url'], PHP_URL_HOST);
			}, $browserResponse['data']['requests']);

			// Remove null and empty results, since sometimes we have data requests
			// An alternative approach would be to validate the URL schema inside
			// the mapping function above
			$uniqueDomains = array_filter($uniqueDomains, 'strlen');

			// Get only unique domains and reindex the numeric array
			$uniqueDomains = collect(array_values(array_unique($uniqueDomains)));

			// Check which of those domains are blocked and on which lists
			$matches = Hostname::whereIn('hostname', $uniqueDomains)->get()->pluck('hostname')->unique();
			$negative = $uniqueDomains->diff($matches);

			// Add results to the main response array
			$response['domains']['blocked'] = $matches;
			$response['domains']['allowed'] = $negative;

		}

		return response($response)->header('Content-Type', 'application/json');;

    }
}

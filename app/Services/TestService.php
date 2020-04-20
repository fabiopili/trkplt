<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use GuzzleHttp\Client;

use App\BlockList;
use App\Hostname;
use App\Test;
use App\TrackerRadarDomains;

class TestService
{

	public function __construct(Test $test)
	{

		// Test object passed by the queue worker
		$this->test = $test;

		// Headless broser response object
		$this->browserResponse = null;

		// Main response objects
		$this->matches = null;
		$this->negatives = null;

		// Begin processing the test
		$this->process();

	}

    /**
     * Process all operations necessary to handle a new test from end to end.
     * This is the actual function that'll be called from our workers
     *
     * @return void
     */
	public function process()
	{

		// Fetch data
		// If an error occurred, halt processing, save and return false.
		if(!$this->fetch()){
			$this->save();
			return false;
		}

		// Match domains to the blocklists
		$this->match();

		// Enrich results
		$this->enrich();

		// Save updated test
		$this->save();

		return true;

	}

    /**
     * Use the headless browser bridge to fetch all URL requests
     *
     * @return void
     */
	private function fetch()
	{

        $cacheTime = 86400; // 24 hours cache for debugging

        // Guzzle instance
       $HTTPClient = new Client();

        $this->browserResponse = Cache::remember($this->test->url, $cacheTime, function() use ($HTTPClient) {

            // Fetch content using the bridge API running on Node.js
            return json_decode(
                $HTTPClient->request('POST', config('app.browser_bridge'), [
                    'json' => ['url' => $this->test->url],
                    'timeout' => 60,
                    'connect_timeout' => 60,
                    'read_timeout' => 60,
                ])->getBody()->getContents()
            , true);

        });

        // Update test with response and set a completed_at datetime
        if(empty($this->browserResponse['data']) || $this->browserResponse['meta']['error'] === true){
            $this->test->raw_results = $this->browserResponse;
            $this->test->is_error = true;
            return false;
        } else {
            $this->test->raw_results = $this->browserResponse;
            $this->test->is_error = false;
            return true;
        }

	}

    /**
     * Match unique hostnames with blocklists
     *
     * @return void
     */
	private function match()
	{

        // Parse all requests to a new array with only the unique domains
        $uniqueDomains = array_map(function($request){
            return parse_url($request['request_url'], PHP_URL_HOST);
        }, $this->test->raw_results['data']['requests']);

        // Remove null and empty results, since sometimes we have inline data:// requests
        // An alternative approach would be to validate the URL schema inside
        // the mapping function above
        $uniqueDomains = array_filter($uniqueDomains, 'strlen');

        // Get only unique domains and reindex the numeric array
        // Move it to a collection so we can leverage Laravel native functions
        $uniqueDomains = collect(array_values(array_unique($uniqueDomains)));

        // Check which of those domains are blocked and on which lists
        // Get all blocklists from the matches
        $rawMatches = Hostname::whereIn('hostname', $uniqueDomains)->with('blockLists:block_lists.name,block_lists.url')->get();

        $rawNegatives = $uniqueDomains->diff($rawMatches->pluck('hostname')->unique());

        // Init PHP Domain Parser
        // https://github.com/jeremykendall/php-domain-parser
        $domainParser = new \Pdp\Manager(new \Pdp\Cache(), new \Pdp\CurlHttpClient());
        $domainParserRules = $domainParser->getRules();

        // Parse domains for each match
        // Remember: hostname != domain
        // And we need the bare domain to be able to group requests and
        // bring external information about the entities behind each request
        $this->matches = $rawMatches->map(function ($item, $key) use ($domainParserRules) {

            // Parse hostname
            $domainData = $domainParserRules->resolve($item->hostname);

            return [
                'hostname' => $item->hostname,
                'domain' => $domainData->getRegistrableDomain(),
                'blockLists' => $item->blockLists,
            ];

        });

        // Parse domains for negatives
        $this->negatives = $rawNegatives->map(function ($item, $key) use ($domainParserRules) {

            // Parse hostname
            $domainData = $domainParserRules->resolve($item);

            return [
                'hostname' => $item,
                'domain' => $domainData->getRegistrableDomain()
            ];

        });

        // Update main test object with the aggregates
        $this->test->nu_blocked = $rawMatches->count();
		$this->test->nu_allowed = $rawNegatives->count();

	}

    /**
     * Match unique hostnames with blocklists
     *
     * @return void
     */
	private function enrich()
	{

        // Enrich results with DuckDuckGo Tracker Radar database
        // Get all domains mentioned at once so we avoid a recursion problem
        $domainsInfo = TrackerRadarDomains::whereIn('domain', $this->matches->pluck('domain')->unique())->get();

        // Make sure we have results to work with
        if($domainsInfo){

            // Create an array indexed by domain for all results we have rich data for
            $domainsInfo = $domainsInfo->mapWithKeys(function ($item) {
                return [$item->domain => $item->data];
            });

            // Enrich matches object
            $this->matches = $this->matches->map(function ($item, $key) use ($domainsInfo) {

                if($item['domain']){
                    if(isset($domainsInfo[$item['domain']])){
                        $item['info'] = [
                            'owner' => $domainsInfo[$item['domain']]['owner'],
                            'categories' => $domainsInfo[$item['domain']]['categories']
                        ];
                    } else {
                        $item['info'] = null;
                    }
                }
                return $item;
            });

        }

	}

    /**
     * Save completed test results
     *
     * @return void
     */
    private function save()
    {

        // Add results to the main object
        $this->test->results = [
            'blocked' => $this->matches,
            'allowed' => $this->negatives,
        ];

    	// Set processing tag
    	$this->test->is_processing = false;
        $this->test->is_done = true;

		try {
		    $this->test->save();
		}
		catch (exception $e) {
			// Todo:
			// Catch error and throw a better exception
		    return false;
		}
		finally {
			return true;
		}

    }

}
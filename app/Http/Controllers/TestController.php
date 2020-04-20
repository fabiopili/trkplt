<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Cog\Laravel\Optimus\Facades\Optimus;

use App\Events\NewTest;
use App\Interfaces\TestInterface;

class TestController extends BaseController
{

    public function __construct(TestInterface $tests, Request $request)
    {

    	// Main request object
        $this->request = $request;

        // Main Test object
        $this->tests = $tests;

        // Hosts that are allowed to access this API
        $this->allowedDomains = ['trkplt.com', 'trkplt.test'];

        // Base return object for all test operations like get and create
        $this->response = [
            'tests' => [],
            'meta' => [
                'error' => false
            ],
        ];
    }

	/**
	 * Check request origin header
	 **/
    private function checkOrigin() {

        // Origin header is required
        if(!$this->request->header('origin')){
			return false;
        }

        // Check if the origin header is set
        if(empty($this->request->header('origin'))){
            return false;
        }

        // Origin header must be a valid URL
        if(!in_array(parse_url($this->request->header('origin'), PHP_URL_SCHEME), ['https', 'http']) || !parse_url($this->request->header('origin'), PHP_URL_HOST)){
            return false;
        }

        $origin = parse_url($this->request->header('origin'));

        // Host is required
        if(empty($origin['host'])){
        	return false;
        }

        if(in_array($origin['host'], $this->allowedDomains)){
            return $origin['scheme'] . "://" . $origin['host'];
        } else {
            // If not allowed, return false
            return false;
        }

    }

	/**
	 * XHR preflight
	 * Check if the domain is allowed and return CORS configuration and security headers
	 * Returns a single domain as multiple ‘Access-Control-Allow-Origin’ is not allowed
	 **/
    public function preflight()
    {

        if($origin = $this->checkOrigin()){

            return response('')
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, X-CSRF-TOKEN, X-XSRF-TOKEN, X-Requested-With');

        } else {
            abort(403);
        }

    }

	/**
	 * Initializes a new test
	 * Returns the encoded test id
	 **/
	public function submit()
    {

    	// Validate URL submitted for testing
        if(in_array(parse_url($this->request['data']['url'], PHP_URL_SCHEME), ['https', 'http']) && parse_url($this->request['data']['url'], PHP_URL_HOST)){
            $url = $this->request['data']['url'];
        } else {
            $response['meta']['error'] = true;
            $response['meta']['error_description'] = 'Invalid URL';
            return $response;
        }

        // Validations
        if(empty($url) || empty($this->request->ip())){
            $this->response['meta']['error'] = true;
            $this->response['meta']['error_description'] = 'Invalid request data';
            return response($this->response);
        }

    	// Create test entry on the database
		$newTest = $this->tests->create([
            'ip' => $this->request->ip(),
            'url' => $url,
        ]);

        // Do I have the new test object?
        if($newTest){
            // Dispatch test event with the id as a tag
            // This is the asynchronous process that'll handle the actual test processing
            // Managed by a Redis queue
            event(new NewTest($newTest));
        } else {
            $this->response['meta']['error'] = true;
            $this->response['meta']['error_description'] = 'An error occurred while creating the test';
            return response($this->response);
        }

        // Return object
        $this->response['tests'] =
        [
            'id' => Optimus::encode($newTest->id),
            'created_at' => $newTest->created_at,
            'url' => $newTest->url,
            'is_processing' => $newTest->is_processing,
            'is_done' => $newTest->is_done,
            'is_error' => $newTest->is_error,
        ];

        return response($this->response);

    }

	/**
	 * Fetch data for a collection of tests
	 **/
    public function index()
    {

        // Make sure I have the ids array on the route
        if(empty($this->request->route('ids'))){
            $this->response['meta']['error'] = true;
            $this->response['meta']['error_description'] = 'Could not find tests';
            return response($this->response);
        }

        // Decode id values
        $ids = array_map(function($id){
            return Optimus::decode((int)$id);
        }, explode(',', $this->request->route('ids')));

        // Get data from the repository
        $tests = $this->tests->index($ids);

        // Format results according to the public response object
        foreach ($tests as $key => $test) {

            $this->response['tests'][] =
            [
                'id' => Optimus::encode($test->id),
                'created_at' => $test->created_at,
                'url' => $test->url,
                'host' => str_replace("www.", "", parse_url($test->url, PHP_URL_HOST)),
                'nu_blocked' => $test->nu_blocked,
                'nu_allowed' => $test->nu_allowed,
                'is_processing' => $test->is_processing,
                'is_done' => $test->is_done,
                'is_error' => $test->is_error
            ];
        }

		return response($this->response);

    }

    /**
     * Fetch data for a collection of tests
     **/
    public function get()
    {

        // Make sure I have the ids array on the route
        if(empty($this->request->route('ids'))){
            $this->response['meta']['error'] = true;
            $this->response['meta']['error_description'] = 'Could not find tests';
            return response($this->response);
        }

        // Decode id values
        $ids = array_map(function($id){
            return Optimus::decode((int)$id);
        }, explode(',', $this->request->route('ids')));

        // Get data from the repository
        $tests = $this->tests->get($ids);

        // Format results according to the public response object
        foreach ($tests as $key => $test) {

            $this->response['tests'][] =
            [
                'id' => Optimus::encode($test->id),
                'created_at' => $test->created_at,
                'url' => $test->url,
                'host' => str_replace("www.", "", parse_url($test->url, PHP_URL_HOST)),
                'nu_blocked' => $test->nu_blocked,
                'nu_allowed' => $test->nu_allowed,
                'is_processing' => $test->is_processing,
                'is_done' => $test->is_done,
                'is_error' => $test->is_error,
                'results' => $test->results,
                'meta' => [
                    'time' => $test->raw_results['meta']['time'] ?? null,
                    'error' => (bool) $test->is_error,
                    'error_message' => $test->raw_results['meta']['error_message'] ?? null
                ],
            ];
        }

        return response($this->response);

    }

}

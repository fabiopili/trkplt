<?php

/*
--------------------------------------------------------------------------
Robots.txt
--------------------------------------------------------------------------
*/
Route::get('robots.txt', function () {
    $response = response()->view('public.txt.robots')->header('Content-Type', 'text/plain');
    $response->header('Content-Length',strlen($response->getOriginalContent()));
    return $response;
});

/*
--------------------------------------------------------------------------
DIRECT ACCESS FOR TEST RESULTS
--------------------------------------------------------------------------
*/
Route::get('/result/{id}', 'AppController@showResult')->where('id', '[0-9]+');

/*
--------------------------------------------------------------------------
API ROUTES
--------------------------------------------------------------------------
*/
Route::prefix('API')->group(function () {

	// Respond to preflight CORS requests
	Route::options('/', 'TestController@preflight')->middleware('api');

	// Begin a new test
	Route::post('/', 'TestController@submit')->middleware('api');

	// Return a simplified index with all tests
	Route::get('/{ids}', 'TestController@index')->where('ids', '[0-9|,]+')->middleware('api');

	// Retrieve or Watch a test
	Route::get('/test/{ids}', 'TestController@get')->where('ids', '[0-9|,]+')->middleware('api');

	// CSRF Token
    Route::get('/csrfToken', function () {
    	return csrf_token();
    });

});

/*
--------------------------------------------------------------------------
DEBUG
--------------------------------------------------------------------------
*/
if(App::environment() === 'local'){
	Route::get('/debug', 'DebugController@handle');
}

/*
--------------------------------------------------------------------------
CatchAll route for all Vue routes
--------------------------------------------------------------------------
*/
Route::get('/{path?}', 'AppController@inject')->where('path', '[\/\w\.-]*');

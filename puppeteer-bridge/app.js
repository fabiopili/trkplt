// Init Express
const express = require("express");
const app = express();

// Read .env files
const dotenv = require('dotenv').config();

// Process JSON requests
app.use(express.json());

// Performance Timing API
// https://nodejs.org/api/perf_hooks.html#perf_hooks_class_performance
const { PerformanceObserver, performance } = require('perf_hooks');

// Init Puppeteer
const puppeteer = require('puppeteer-core');

// Log init state
app.listen(4444, () => {
	console.log("Server running on port 4444");
});

// Sample JSON response
app.get("/alive", (req, res, next) => {
	res.json(true);
});

// Try Puppeteer
app.post("/", (req, res, next) => {

	const url = req.body.url;

	// Track execution time
	const startTime = performance.now();

	// Log URL
	console.log("Fetching URL " + url);

	(async () => {

		const browser = await puppeteer.connect({
			browserWSEndpoint: 'wss://chrome.browserless.io?token=' + process.env.BROWSERLESS_TOKEN,
		});

		// Main results array
		const result = {
			'data': {
				'requests': []
			},
			'meta': {
				'url': url,
				'time': null,
				'error': false
			}
		};
		const page = await browser.newPage();

		// Set user agent
		await page.setUserAgent(process.env.USER_AGENT);

		// Log but skip downloading some resources
		// to save bandwidth and process the page faster
		const ignoredResources = [
		  'image',
		  'media',
		  'font',
		  'object',
		  'imageset',
		];

		// Intercept all requests
  		await page.setRequestInterception(true);

		await page.on('request', async (request) => {

			// Define base response object
			const thisRequest = {
				'request_url': request.url(),
				'resource_type': request.resourceType()
			};

			// Add this request to main results object
			await result.data.requests.push(thisRequest);

			if (ignoredResources.indexOf(request.resourceType()) !== -1) {
				if(process.env.APP_ENV === 'local'){
					console.log('Ignoring resource ' + request.resourceType() + ' ' + request.url());
				}
				request.abort();
			} else {
				if(process.env.APP_ENV === 'local'){
					console.log('Fetching resource ' + request.resourceType() + ' ' + request.url());
				}
				request.continue();
			}

		});

		try {

			const response = await page.goto(url, {
				timeout: process.env.FETCH_TIMEOUT,
				waitUntil: 'networkidle2'
			});

			// HTTP error
			if (response.statusCode < 200 || response.statusCode > 299) {
				console.log("Failed to load " + url + " Status code: " + response._status);
		    	await browser.close();
		    	result.meta.error = true;
		    	result.meta.error_message = 'Failed to load URL, status code ', response._status;
		    	res.json(result);
			}

	    } catch (err) {
	    	// Error
	    	console.log("Error for " + url + " Error: " + err);
	    	await browser.close();
	    	result.meta.error = true;
	    	result.meta.error_message = 'Failed to process URL. ', err;
	    	res.json(result);
	    }

	    // Return results
		await browser.close();

		// Track execution time
		const endTime = performance.now();
		result.meta.time = parseFloat(((endTime - startTime) / 1000).toFixed(2));

		// Return results
		res.json(result);

	})().catch(error=>{});


});
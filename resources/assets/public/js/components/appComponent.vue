/*
-----------------------------------------------------------------------
MAIN APP COMPONENT
-----------------------------------------------------------------------
*/
<template>
	<div id="main">
		<img src="/assets/trkplt/images/logo.svg" class="logo">

		<div class="mainContent">

			<div class="bg"></div>

			<headerComponent></headerComponent>

			<div class="overview results inner" v-if="results">
				<h2>Your results</h2>

				<router-link tag="div" class="result" v-bind:class="{ done: result.is_done }" :to="'/result/' + result.id" title="View result" v-for="(result, key) in results" :id="'results_row_' + key" :key="result.id" v-on:click="switchFocusTo(result.id)">

					<div class="host">
						<span v-if="result.host">
							{{ result.host }}
						</span>
						<span v-else>
							<img src="/assets/trkplt/images/loader.svg" class="inlineLoader">
						</span>

					</div> <!-- .host -->

					<div class="bar" v-if="!result.is_error">
						<div class="progress" v-if="result.host && !result.is_error && result.is_done" :style="{'width': (result.nu_blocked / (result.nu_allowed + result.nu_blocked)) *  100 + '%'}"></div>
					</div>
					<div v-else></div>

					<div class="summary">
						<div v-if="!result.is_error && result.is_done">
							<span>{{ result.nu_blocked }}</span> / <span>{{ (result.nu_allowed + result.nu_blocked) }}</span> blocked
						</div>
						<div v-else>
							<img v-if="!result.is_error" src="/assets/trkplt/images/loader.svg" class="inlineLoader">
							<div v-bind:class="{ isError: result.is_error }" v-else>Error</div>
						</div>
					</div> <!-- .summary -->

					<div class="commands">
						<svg class="icon"><use xlink:href="#icon-arrow-right" /></svg>
					</div>

				</router-link> <!-- .result -->

			</div> <!-- .results -->

			<div class="sample results inner" v-if="sampleResults">
				<h2>Sample results</h2>

				<router-link tag="div" class="result" v-bind:class="{ done: result.is_done }" :to="'/result/' + result.id" title="View result" v-for="(result, key) in sampleResults" :id="'sample_row_' + key" :key="result.id" v-on:click="switchFocusTo(result.id)">

					<div class="host">
						<span v-if="result.host">
							{{ result.host }}
						</span>
						<span v-else>
							<img src="/assets/trkplt/images/loader.svg" class="inlineLoader">
						</span>

					</div> <!-- .host -->

					<div class="bar">
						<div class="progress" v-if="result.host && !result.is_error && result.is_done" :style="{'width': (result.nu_blocked / (result.nu_allowed + result.nu_blocked)) *  100 + '%'}"></div>
					</div>

					<div class="summary">
						<div v-if="!result.is_error && result.is_done">
							<span>{{ result.nu_blocked }}</span> / <span>{{ (result.nu_allowed + result.nu_blocked) }}</span> blocked
						</div>
						<div v-else>
							<img src="/assets/trkplt/images/loader.svg" class="inlineLoader">
						</div>
					</div> <!-- .summary -->

					<div class="commands">
						<svg class="icon"><use xlink:href="#icon-arrow-right" /></svg>
					</div>

				</router-link> <!-- .result -->

			</div> <!-- .results -->

			<div class="inner why">
				<div class="innerHeader">
					<h2>Why? If you're <br class="show-for-small-only" />a <span class="highlight">developer</span></h2>
				</div>
				<div class="content">
					<div class="box col1">
						<div class="dot">1</div>
						<h4>Be mindful</h4>
						<p>Tracking and measuring user behavior is key for taking data-driven decisions and uncovering bugs. But, sometimes, this is done at the expense of user experience.</p>
						<p>Don't mindlessly install external tools. There's always a privacy and performance penalty involved.</p>
					</div> <!-- .col1 -->
					<div class="box col2">
						<div class="dot">2</div>
						<h4>Privacy is a feature</h4>
						<p>There is real market demand for privacy-minded services.</p>
						<p>Be upfront about all data collected, specially by third-party trackers and services.</p>
						<p>If possible, anonymize user data and avoid sharing it with third-parties.</p>
						<p>Always allow opt-out.</p>
					</div> <!-- .col2 -->
					<div class="box col3">
						<div class="dot">3</div>
						<h4>Degrade gracefully</h4>
						<p>Depending on the source, between <a href="https://blogs.harvard.edu/doc/2019/03/23/2billion/">11%</a> and <a href="https://www.digitalinformationworld.com/2019/04/global-ad-blocking-behaviors-infographic.html">47%</a> of all internet users are currently blocking ads.</p>
						<p>Always check if an external resource is available before firing any event and degrade gracefully to avoid breaking the code flow.</p>
						<p>Don't rely on third-party tools for critical app functionality.</p>
					</div> <!-- .col3 -->
				</div> <!-- .content -->
			</div> <!-- .why -->

			<div class="inner why">
				<div class="innerHeader">
					<h2>Why? If you're <br class="show-for-small-only" />an <span class="highlight">end-user</span></h2>
				</div>
				<div class="content">
					<div class="box col1">
						<div class="dot">1</div>
						<h4>Legitimate uses</h4>
						<p>Not all web tracking is evil.</p>
						<p>Recognizing users and devices is necessary to provide functionality like personalization, content suggestions and to identify bugs.</p>
						<p>The problem is when data is <a href="https://www.freecodecamp.org/news/what-you-should-know-about-web-tracking-and-how-it-affects-your-online-privacy-42935355525/">silently collected by third-parties</a> and without explicit user consent.</p>
					</div> <!-- .col1 -->
					<div class="box col2">
						<div class="dot">2</div>
						<h4>Know your data</h4>
						<p>Data is a commodity.</p>
						<p><a href="https://www.wired.com/story/wired-guide-personal-data-collection/">Big data aggregators collect, cross-reference and sell user data</a> that will be used to power all kinds of services, from advertising to political propaganda.</p>
						<p>Be mindful about what you share online, knowingly or not.</p>
					</div> <!-- .col2 -->
					<div class="box col3">
						<div class="dot">3</div>
						<h4>Learn more</h4>
						<p><a href="https://blog.mozilla.org/firefox/what-is-a-web-tracker/">What is a web tracker?</a></p>
						<p><a href="https://spreadprivacy.com/private-tools-remote-work/">Working from Home? Consider These Privacy-Focused Tools</a></p>
						<p><a href="https://www.wired.com/story/wired-guide-personal-data-collection/">The WIRED Guide to Your Personal Data (and Who Is Using It)</a></p>
					</div> <!-- .col3 -->
				</div> <!-- .content -->
			</div> <!-- .why -->

		</div> <!-- .mainContent -->

	</div> <!-- #main -->
</template>

<script>

	import headerComponent from './header.vue';

    export default {
    	props: ['processing'],
		data: function () {
		  return {
		  	url: null,
		  	isProcessing: false,
		  	timer: null,
		  	results: null,
		  	sampleResults: {
		  		0: {
		  			id: '121740723',
		  			created_at: '2020-04-16T21:13:21.000000Z',
		  			url: 'https://www.nytimes.com/',
		  			host: 'nytimes.com',
		  			nu_blocked: 37,
		  			nu_allowed: 27,
		  			is_processing: false,
		  			is_done: true,
		  			is_error: false
		  		}
		  	},
		  }
		},
		computed: {
			// Fetch test list from the Vuex store
			tests: function () {
				return this.$store.getters.tests;
			},
		},
    	components: {
    		headerComponent,
    	},
		watch: {
			tests: {
				handler() {
					if(this.tests){
						this.fetchData();
					}
				}
			},
			results: {
				deep: true,
				handler() {
					if(this.results){
						this.updateIsProcessing();
					}
				}
			},
			// Watch the isProcessing variable to toggle periodic polling on/off
			isProcessing: {
				handler() {
					if(this.isProcessing && !this.timer){
						this.beginPolling();
					}
					if(!this.isProcessing){
						this.stopPolling();
					}
				}
			}
		},
    	methods: {

			// Whatch tests to see if any is still processing
			updateIsProcessing() {

				let pendingResults = this.results.filter(result => {
					if(result.is_processing){
						return true;
					}
	    		});

	    		if(pendingResults.length > 0){
	    			this.isProcessing = true;
	    		} else {
	    			this.isProcessing = false;
	    		}

			},

			// Watch backend for tests status
			beginPolling(){
				console.log('Begin polling');
				this.timer = setInterval(this.fetchData, 2000);
			},

			// Stop polling backend for tests status
			stopPolling(){
				console.log('Stop polling');
				clearInterval(this.timer);
			},

			// Fetch data from backend API
			fetchData() {

				if(this.tests.length == 0){
					return false;
				}

				console.log('Fetching');

				axios.get('/API/' + this.tests, {
					dataType: 'json'
				})
				.then(response => {
					this.results = response.data.tests;
				})
				.catch(error => {
					this.stopPolling();
				});

			},

    	},
		created() {

			// this.$store.commit('resetTests');

			// Shameless plug
			console.log("%c 👋 I'm available for new projects. Let's talk! hello@fabiopili.com", "font-weight: bold; color: rgb(90, 10, 176)");

			// Load all tests on app boot
			this.fetchData();

		},
		// Make sure any periodic polling is stoped when the component is destroyed
		beforeDestroy() {
			this.stopPolling();
		}


    }

</script>
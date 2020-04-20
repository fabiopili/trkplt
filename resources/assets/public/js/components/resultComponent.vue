/*
-----------------------------------------------------------------------
DETAILED RESULT COMPONENT
-----------------------------------------------------------------------
*/
<template>
	<div id="main">

		<router-link :to="{ path: '/' }">
			<img src="/assets/trkplt/images/logo.svg" class="logo noline">
		</router-link>

		<div class="mainContent">

			<div class="bg"></div>

			<div class="results">
				<div class="detailed" v-if="result" v-bind:class="{ isError: result.is_error, done: result.is_done }">

					<div class="summary">

							<div class="host">
								<h1>{{ result.host }}</h1>
								<div class="url">
									<a :href="result.url">{{ result.url }}</a>
								</div>
							</div> <!-- .host -->

							<div class="bar">
								<div class="progress" v-if="!result.is_error && result.is_done" :style="{'width': (result.nu_blocked / (result.nu_allowed + result.nu_blocked)) *  100 + '%'}"></div>
							</div> <!-- .bar -->

							<div class="versus">

								<div class="number blocked">
									<span v-if="result.nu_blocked !== null">
										{{ result.nu_blocked }}
									</span>
									<span v-else-if="result.is_error">
										X
									</span>
									<span v-else>
										?
									</span>
									<div class="label">blocked</div>
								</div>

								<div class="separator">
									<svg class="icon"><use xlink:href="#icon-large-x" /></svg>
								</div>

								<div class="number allowed">
									<span v-if="result.nu_allowed !== null">
										{{ result.nu_allowed }}
									</span>
									<span v-else-if="result.is_error">
										X
									</span>
									<span v-else>
										?
									</span>
									<div class="label">allowed</div>
								</div>

							</div> <!-- .versus -->

							<div class="meta" v-if="result.meta && !result.is_error">
								loaded in <span>{{ result.meta.time }} seconds</span>, <br class="show-for-small-only" />ignoring all images, media and fonts
							</div>
							<div class="meta" v-if="result.is_error">
								<span>Error: </span>{{ result.meta.error_message }}
							</div>

					</div> <!-- .summary -->

					<div class="details inner" v-if="result.results && !result.is_error">

						<h2 v-if="result.results.blocked[0]">Blocked requests</h2>

						<div class="request" v-for="(hostname, key) in result.results.blocked" :id="'hostname_row_' + key">

							<div class="anchor">
								<div class="circle">
									<svg class="icon"><use xlink:href="#icon-slash" /></svg>
								</div>
							</div> <!-- .anchor -->

							<div class="requestContent">

								<h3>{{ hostname.hostname }}</h3>

								<div v-if="hostname.info">
									<div class="categories" v-if="hostname.info.categories[0]">
										<div class="tag" v-if="hostname.info.categories" v-for="(category, key) in hostname.info.categories">
											{{ category }}
										</div>
									</div> <!-- .categories -->
								</div>

								<div v-if="hostname.info">
									<div class="owner" v-if="hostname.info.owner">
										<div class="label">
											Owner
										</div>
										<div class="item">
											<span v-if="hostname.info.owner.url">
												<a :href="hostname.info.owner.url">{{ hostname.info.owner.displayName }}</a>
											</span>
											<span v-else>
												{{ hostname.info.owner.displayName }}
											</span>
											<span v-if="hostname.info.owner.privacyPolicy">
												 • <a :href="hostname.info.owner.privacyPolicy">Privacy Policy</a>
											</span>
										</div>
									</div> <!-- .owner -->
								</div>

								<div class="blocklist" v-if="hostname.blockLists">
									<div class="label">
										Blocked by
									</div>
									<div class="item" v-for="(blockList, key) in hostname.blockLists">
										<a :href="blockList.url">{{ blockList.name }}</a>
									</div>
								</div> <!-- .blocklist -->

							</div> <!-- .requestContent -->

						</div>

					</div> <!-- .details -->

					<div class="credits inner">

						<a href="https://github.com/duckduckgo/tracker-radar" class="noline">
							<img src="/assets/trkplt/images/DuckDuckGo.svg">
						</a>
						<div class="byline">
							Tracker data provided by <br class="show-for-small-only" /><a href="https://github.com/duckduckgo/tracker-radar" class="discrete">DuckDuckGo Tracker Radar</a>
						</div>
					</div> <!-- .credits -->

				</div> <!-- .result -->

			</div> <!-- .results -->

		</div> <!-- .mainContent -->

	</div> <!-- #main -->
</template>

<script>

	import headerComponent from './header.vue';
	import plotComponent from './plot.vue';

    export default {
    	props: ['processing'],
		data: function () {
		  return {
		  	url: null,
		  	isProcessing: false,
		  	timer: null,
		  	result: null,
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
    		plotComponent,
    	},
		watch: {
			tests: {
				handler() {
					if(this.tests){
						this.fetchData();
					}
				}
			},
			result: {
				deep: true,
				handler() {
					if(this.result){
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

			// Watch tests to see if any is still processing
			updateIsProcessing() {

				if(this.result.is_processing){
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

				if(typeof this.$route.params.id === 'undefined'){
					return false;
				}

				console.log('Fetching');

				axios.get('/API/test/' + this.$route.params.id, {
					dataType: 'json'
				})
				.then(response => {
					this.result = response.data.tests[0];
				})
				.catch(error => {
					this.stopPolling();
				});

			},

    	},
		created() {

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
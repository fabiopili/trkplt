/*
-----------------------------------------------------------------------
HEADER COMPONENT
-----------------------------------------------------------------------
*/

<template>
	<div class="header">
		<div class="content">
			<h1 class="tagline">
				<span class="nowrap">Test a <span class="highlight">website</span> with the</span>
				<span class="nowrap">most popular <span class="highlight">ad blocking</span> lists</span>
			</h1>
			<div class="controls">
				<form method="POST" action="#" @submit.prevent="onSubmit">
					<input type="text" id="url" name="url" placeholder="https://" v-model="url">
					<button class="submit" :disabled="isProcessing">Go!</button>
				</form>
			</div> <!-- .controls -->
			<div class="error" v-if="formError">{{ formError }}</div>
		</div> <!-- .content -->
	</div>
</template>

<script>

	import { required, url } from 'vuelidate/lib/validators'

    export default {
		data: function () {
		  return {
		  	isProcessing: false,
		  	url: null,
		  	formError: null,
		  }
		},
		validations: {
			url: {
				required,
				url
			},
		},
    	methods: {

    		// Submit new test for processing
			onSubmit() {

				// Make sure form passes validation
				if(!this.$v.$invalid) {

					// Add to queue
					axios.post('/API/', {
						dataType: 'json',
						data: {
							url: this.url
						}
					})
					.then(response => {
						this.newTestSaved(response);
						this.resetForm();
					})
					.catch(error => {
						this.resetForm();
					});

				} else {
					this.formError = "Invalid URL";
				}

			},

			// Reset form
			resetForm() {

				// Reset form when processing is complete
				this.url = null;

				// Toggle processing flag
				this.isProcessing = false;

				// Reset form error
				this.formError = null;

			},

			// Handle the response after a new test is saved
			newTestSaved(response) {

				// Update main Vuex store with the new test
				this.$store.commit('addNewTest', response.data.tests.id)

				// Redirect to the home page, if not already there
				if(typeof this.$route.params.id !== 'undefined'){
					router.push({ path: '/' })
				}

			}

    	},
    }

</script>
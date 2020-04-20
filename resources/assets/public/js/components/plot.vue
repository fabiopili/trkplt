/*
-----------------------------------------------------------------------
HEADER COMPONENT
-----------------------------------------------------------------------
*/

<template>
	<div class="plot">
		<div class="drop" v-for="(drop, key) in drops" :width="randomSize()">
		</div>
	</div>
</template>

<script>

    export default {
    	props: ['result'],
		computed: {
			// Fetch test list from the Vuex store
			drops: function () {
				return this.result.results.allowed;
			},
		},
    	methods: {
			randomSize : function(){
				return Math.floor(Math.random() * (10 - 1 + 1)) + 1;
			}

    	}
    }

</script>
/*
-----------------------------------------------------------------------
REFRESH CSRF META TAG PERIODICALLY
-----------------------------------------------------------------------
*/

<template>
	<span id="csrf"></span>
</template>

<script>
    export default {
		data: function() {
			return {
				timer: ''
			}
		},
		created: function() {
			this.timer = setInterval(this.updateCSRFToken, 600000)
		},
		methods: {
			updateCSRFToken: function() {

			    axios.get("/API/csrfToken")
			    .then(response => {
			    	var CSRFmeta = document.querySelector("[name=csrf-token]");
			    	CSRFmeta.setAttribute("content", response.data);
			    })

			},
			cancelAutoUpdate: function() {
				clearInterval(this.timer)
			}
		},
		beforeDestroy() {
			clearInterval(this.timer)
		}
    }
</script>

<script src="{{ mix('/assets/trkplt/js/app.js') }}"></script>

<div id="svg-icons-container">
	<?php include(public_path() . '/assets/trkplt/icons/icons.svg'); ?>
</div>

@if(App::environment('production'))
	<script async defer data-domain="trkplt.com" src="https://plausible.io/js/plausible.js"></script>
@endif
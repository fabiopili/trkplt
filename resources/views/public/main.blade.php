<!doctype html>
<html lang="en">
    @include('public.components.head')
    <body>

        <div id="app">

			<div id="processing" :class="{ isProcessing: processing }">
				<div class="progress">
					<div class="indeterminate"></div>
				</div>
			</div>

        	<div id="innerWrapper" :class="{ isProcessing: processing }">
            	<router-view :processing="processing"></router-view>
            </div> <!-- #innerWrapper -->

        	<csrf></csrf>

            @include('public.components.footer')

        </div> <!-- #app -->

    @include('public.components.closeBody')
    </body>
</html>
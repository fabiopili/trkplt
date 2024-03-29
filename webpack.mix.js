// Initialize Laravel Mix
const mix = require('laravel-mix');

// Initiate extra modules
const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');

// Disable notifications on MacOS
// mix.disableNotifications();

// https://github.com/JeffreyWay/laravel-mix/issues/287
// https://github.com/JeffreyWay/laravel-mix/issues/232
mix.options({ processCssUrls: false });

/*
-----------------------------------------------------------------------
PUBLIC
-----------------------------------------------------------------------
*/
// JS
mix.js([
    'resources/assets/public/js/app.js',
    'resources/assets/public/js/font.js',
],
'public/assets/trkplt/js/app.js')
.vue({ version: 2 })
// https://github.com/vuejs/vue-next/tree/master/packages/vue#bundler-build-feature-flags
.webpackConfig((webpack) => {
    return {
        plugins: [
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: true,
                __VUE_PROD_DEVTOOLS__: false,
            }),
        ],
    };
})
.version();

// SASS
mix.sass(
    'resources/assets/public/sass/app.scss',
    'public/assets/trkplt/css/app.css'
).version();

// IMAGES
mix.copyDirectory('resources/assets/public/images', 'public/assets/trkplt/images');

// FONTS
mix.copyDirectory('resources/assets/public/fonts', 'public/assets/trkplt/fonts');

/*
-----------------------------------------------------------------------
ERROR PAGES
-----------------------------------------------------------------------
*/
// SASS
mix.sass(
    'resources/assets/public/sass/error.scss',
    'public/assets/trkplt/css/error.css'
).version();

/*
-----------------------------------------------------------------------
ALL SVG SPRITES AT ONCE
-----------------------------------------------------------------------
*/

mix.webpackConfig({
    // https://github.com/JeffreyWay/laravel-mix/issues/1793
    devtool: 'source-map',
    // https://github.com/JeffreyWay/laravel-mix/issues/879#issuecomment-310749504
    plugins: [
        new SVGSpritemapPlugin(
        	'resources/assets/public/icons/*.svg',
        {
            output: {
                'filename': 'assets/trkplt/icons/icons.svg',
                // https://github.com/cascornelissen/svg-spritemap-webpack-plugin/issues/63
                chunk: {
                    keep: true
                },
                'svgo': {
                    'plugins': [
                        {
                            name: 'cleanupIDs',
                            active: false
                        },
                        {
                            name: 'removeXMLNS',
                            active: true
                        },
                        {
                            name: 'removeDimensions',
                            active: true
                        },
                        {
                            name: 'removeTitle',
                            active: true
                        }
                    ]
                }
            },
            sprite: {
                'prefix': 'icon-',
            }

        }),
    ]
});

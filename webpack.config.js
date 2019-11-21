var Encore = require('@symfony/webpack-encore');

Encore
        // directory where compiled assets will be stored
        .setOutputPath('public/build/')
        // public path used by the web server to access the output path
        .setPublicPath('/SIARPS/public/build')
        // only needed for CDN's or sub-directory deploy
        .setManifestKeyPrefix('build/')

        /*
         * ENTRY CONFIG
         *
         * Add 1 entry for each "page" of your app
         * (including one that's included on every page - e.g. "app")
         *
         * Each entry will result in one JavaScript file (e.g. app.js)
         * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
         */
        .addEntry('app', './assets/js/app.js')
        .addEntry('charts_app', './assets/js/charts.js')
        .addEntry('datatables_app', './assets/js/datatables.js')
        
        .addEntry('users', './assets/js/views/users.js')
        .addEntry('projects', './assets/js/views/projects.js')

//        .addEntry('calendar', './assets/js/views/calendar.js')
//        .addEntry('charts', './assets/js/views/charts.js')
//        .addEntry('code-editor', './assets/js/views/code-editor.js')
//        .addEntry('colors', './assets/js/views/colors.js')
//        .addEntry('datatables', './assets/js/views/datatables.js')
//        .addEntry('draggable-cards', './assets/js/views/draggable-cards.js')
//        .addEntry('google-maps', './assets/js/views/google-maps.js')
//        .addEntry('loading-buttons', './assets/js/views/loading-buttons.js')
//        .addEntry('main', './assets/js/views/main.js')
//        .addEntry('markdown-editor', './assets/js/views/markdown-editor.js')
//        .addEntry('popovers', './assets/js/views/popovers.js')
//        .addEntry('sliders', './assets/js/views/sliders.js')
//        .addEntry('text-editor', './assets/js/views/text-editor.js')
//        .addEntry('toastr', './assets/js/views/toastr.js')
//        .addEntry('tooltips', './assets/js/views/tooltips.js')
//        .addEntry('validation', './assets/js/views/validation.js')
//        .addEntry('widgets', './assets/js/views/widgets.js')



        //.addEntry('page1', './assets/js/page1.js')
        //.addEntry('page2', './assets/js/page2.js')

        // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
        .splitEntryChunks()

        // will require an extra script tag for runtime.js
        // but, you probably want this, unless you're building a single-page app
        .enableSingleRuntimeChunk()

        /*
         * FEATURE CONFIG
         *
         * Enable & configure other features below. For a full
         * list of features, see:
         * https://symfony.com/doc/current/frontend.html#adding-more-features
         */
        .cleanupOutputBeforeBuild()
        .enableBuildNotifications()
        .enableSourceMaps(!Encore.isProduction())
        // enables hashed filenames (e.g. app.abc123.css)
        .enableVersioning(Encore.isProduction())

        // enables @babel/preset-env polyfills
        .configureBabel(() => {
        }, {
            useBuiltIns: 'usage',
            corejs: 3
        })



        // enables Sass/SCSS support
        //.enableSassLoader()

        // uncomment if you use TypeScript
        //.enableTypeScriptLoader()

        // uncomment to get integrity="..." attributes on your script & link tags
        // requires WebpackEncoreBundle 1.4 or higher
        //.enableIntegrityHashes()

        // uncomment if you're having problems with a jQuery plugin
        .autoProvidejQuery()

        // uncomment if you use API Platform Admin (composer req api-admin)
        //.enableReactPreset()
        //.addEntry('admin', './assets/js/admin.js')
        ;
var config = Encore.getWebpackConfig();

config.module.rules.unshift({
    parser: {
        amd: false,
    }
});
module.exports = config;

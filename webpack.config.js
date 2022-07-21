var Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/")
    // public path used by the web server to access the output path
    .setPublicPath("/build")
    // only needed for CDN's or sub-directory deploy
    // .setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.scss) if your JavaScript imports CSS.
     */
    .addEntry("app", "./assets/js/app.js")

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge("./assets/controllers.json")

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
    .cleanupOutputBeforeBuild(["public"], (options) => {
        options.verbose = true;
        options.root = __dirname;
        options.exclude = ["emails"];
    })
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push("@babel/plugin-proposal-class-properties");
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage";
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()
    // .enablePostCssLoader();

    .copyFiles({
        from: "./assets/images",
        to: Encore.isProduction()
            ? "images/[path][name].[hash:8].[ext]"
            : "images/[path][name].[ext]",
    })

    .copyFiles([
        {
            from: "./node_modules/ckeditor4/",
            to: "ckeditor/[path][name].[ext]",
            pattern: /\.(js|css)$/,
            includeSubdirectories: false,
        },
        {
            from: "./node_modules/ckeditor4/adapters",
            to: "ckeditor/adapters/[path][name].[ext]",
        },
        {
            from: "./node_modules/ckeditor4/lang",
            to: "ckeditor/lang/[path][name].[ext]",
        },
        {
            from: "./node_modules/ckeditor4/plugins",
            to: "ckeditor/plugins/[path][name].[ext]",
        },
        {
            from: "./node_modules/ckeditor4/skins",
            to: "ckeditor/skins/[path][name].[ext]",
        },
        {
            from: "./node_modules/ckeditor4/vendor",
            to: "ckeditor/vendor/[path][name].[ext]",
        },
    ]);

// build the first configuration
const baseConfig = Encore.getWebpackConfig();

// Set a unique name for the config (needed later!)
baseConfig.name = "baseConfig";

// reset Encore to build the second config
Encore.reset();

Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/emails")
    // public path used by the web server to access the output path
    .setPublicPath("/build")
    // only needed for CDN's or sub-directory deploy
    // .setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.scss) if your JavaScript imports CSS.
     */
    .addStyleEntry("emails", "./assets/styles/emails.scss")

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
    .enableBuildNotifications()

    // enables Sass/SCSS support
    .enableSassLoader();

// build the second configuration
const mailerConfig = Encore.getWebpackConfig();

// Set a unique name for the config (needed later!)
mailerConfig.name = "mailerConfig";

// export the final configuration as an array of multiple configurations
module.exports = [baseConfig, mailerConfig];

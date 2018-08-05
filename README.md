Using Webpack 4 and SASS with WordPress

Begin in the root folder of the WordPress Theme and install Webpack and Webpack CLI: 
  npm i --save-dev webpack webpack-cli

Initialize the project.
  npm init

Within auto generated package.json add scripts for watch commands:
  "scripts": {
    "build": "webpack --mode development",
    "dist": "webpack --mode production",
    "watch": "webpack --watch --mode development"
  },

To organize the source code create a 'js' and 'css' directory inside the theme's root folder. Each has an 'src' which contains source code and a 'build' folder which will be the build target for Webpack compiled assets.

Configuring the JavaScript Build (with Babel and Minification)

incorporate Babel for maximum browser compatibility. Code minification was also plug-and-play using the uglifyjs-webpack-plugin.

To install the Babel and the code minification dependencies, run:
  npm i --save-dev babel-core babel-loader babel-preset-env uglifyjs-webpack-plugin

Configuring the SASS Build
  Webpack 4 introduced the mini-css-extract-plugin, which replaces the ever-popular extract-text-webpack-plugin2. Essentially this plugin allows Webpack to extract CSS code into its own individual file separate from the rest of the JavaScript code. 
  npm i --save-dev sass-loader css-loader node-sass mini-css-extract-plugin optimize-css-assets-webpack-plugin

const path = require('path');

// include the js minification plugin
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

// include the css extraction and minification plugins
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");


webpack.config.js file that contains both the JavaScript and SASS configurations:

  module.exports = {
    entry: ['./js/src/app.js', './css/src/app.scss'],
    output: {
      filename: './js/build/app.js',
      path: path.resolve(__dirname)
    },
    module: {
      rules: [
        // perform js babelization on all .js files
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader",
            options: {
              presets: ['babel-preset-env']
          }
          }
        },
        // compile all .scss files to plain old css
        {
          test: /\.(sass|scss)$/,
          use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader']
        }
      ]
    },
    plugins: [
      // extract css into dedicated file
      new MiniCssExtractPlugin({
        filename: './css/build/main.min.css'
      })
    ],
    optimization: {
      minimizer: [
        // enable the js minification plugin
        new UglifyJSPlugin({
          cache: true,
          parallel: true
        }),
        // enable the css minification plugin
        new OptimizeCSSAssetsPlugin({})
      ]
    }
  };

Loading the compiled assets into WordPress
  // register webpack stylesheet and js with theme
  wp_enqueue_style( 'site_main_css', get_template_directory_uri() . '/css/build/main.min.css' );
  wp_enqueue_script( 'site_main_js', get_template_directory_uri() . '/js/build/app.min.css' , null , null , true );


Include in .gitignore
  /* ignore build and node_modules folders */
  node_modules/
  build/

Versioning the JavaScript and CSS Assets

To version the Webpack compiled JavaScript and CSS assets so that each version could be accessed (and cached) independently. To do this, simply add a hash to the output filenames in my webpack.config.js file:

  // for the JavaScript build
  output: {
    filename: './js/build/app.min.[hash].js',
    path: path.resolve(__dirname)
  }

  // for the SASS/CSS build
  plugins: [
    new MiniCssExtractPlugin({
      filename: './css/build/main.min.[hash].css'
    })
  ]

  Incorporating the clean-webpack-plugin to remove old JavaScript and CSS files from my build directories.
    plugins: [
      // clean out build directories on each build
      new CleanWebpackPlugin(['./js/build/*','./css/build/*']),
    ]


Now that Webpack produces files with unique and versioned filenames, enqueue them in WordPress without knowing the hash value in their filenames, using the following logic in order to do this:

// include the css file
$cssFilePath = glob( get_template_directory() . '/css/build/main.min.*' );
$cssFileURI = get_template_directory_uri() . '/css/build/' . basename($cssFilePath[0]);
wp_enqueue_style( 'site_main_css', $cssFileURI );
// include the javascript file
$jsFilePath = glob( get_template_directory() . '/js/build/app.min.*.js' );
$jsFileURI = get_template_directory_uri() . '/js/build/' . basename($jsFilePath[0]);
wp_enqueue_script( 'site_main_js', $jsFileURI , null , null , true );

Credit to https://taylor.callsen.me/using-webpack-4-and-sass-with-wordpress/# wordpress-child-theme-with-webpack-and-sass

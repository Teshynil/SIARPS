/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require('../css/main.css');
require('@coreui/icons/css/coreui-icons.css');
require('flag-icon-css/css/flag-icon.css');
require('font-awesome/css/font-awesome.css');
require('simple-line-icons/css/simple-line-icons.css');
require('../css/vendor-custom/pace.css');
//JS
const $ = require('jquery');
global.$ = global.jQuery = $;
require('popper.js/dist/umd/popper.js');
require('bootstrap/dist/js/bootstrap.js');
require('./vendor-custom/pace-progress/pace.js');
require('perfect-scrollbar/dist/perfect-scrollbar.js');
require('@coreui/coreui-pro/dist/js/coreui.js');
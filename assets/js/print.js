/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/sheets-of-paper.css');
require('../css/app.css');
require('../css/print.css');
//JS
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap/dist/js/bootstrap.js');

var header = $('.header');
var footer = $('.footer');
$('.page').each(function () {
    $(this).children().wrapAll($('<div class="page-body"></div>'));
    $(this).prepend(header.clone());
    $(this).append(footer.clone());
});
header.remove();
footer.remove();
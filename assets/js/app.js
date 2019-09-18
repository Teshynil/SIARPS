/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require('../css/main.css');
require('@coreui/icons/css/free.css');
require('flag-icon-css/css/flag-icon.css');
require('@fortawesome/fontawesome-free/css/all.css');
require('simple-line-icons/css/simple-line-icons.css');
require('../css/vendor-custom/pace.css');
require('toastr/build/toastr.min.css');
//JS
const $ = require('jquery');
global.$ = global.jQuery = $;
$("[fallback-src]").each(function() {
    this.onerror = function() {
        this.src = this.getAttribute('fallback-src');
    }
});
require('popper.js/dist/umd/popper.js');
require('bootstrap/dist/js/bootstrap.js');
require('@fortawesome/fontawesome-free/js/all.js');
const bootbox = require('bootbox/bootbox.all.js');
global.bootbox = bootbox;
require('./vendor-custom/pace-progress/pace.js');
const PerfectScrollbar = require('perfect-scrollbar/dist/perfect-scrollbar.js');
const toastr = require('toastr/build/toastr.min.js');
global.toastr = toastr;
global.PerfectScrollbar = PerfectScrollbar;
require('./vendor-custom/coreui/coreui.js');
require('./vendor-custom/letterpic/letterpic.js');
$('[bootbox-alert]').each(function() {
    $(this).click(function() {
        bootbox.alert({
            size: $(this).data('bootboxSize') || "sm",
            title: $(this).data('bootboxTitle') || "",
            message: $(this).data('bootboxMessage') || "",
            centerVertical: $(this).data('bootboxVertical') || false
        })
    });
});
$('[data-toggle="tooltip"]').tooltip();
$('[data-toggle="popover"]').popover();
$(".letterpic").letterpic();
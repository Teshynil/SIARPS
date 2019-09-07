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
const bootbox = require('bootbox/src/bootbox.js');
global.bootbox = bootbox;
require('./vendor-custom/pace-progress/pace.js');
const PerfectScrollbar = require('perfect-scrollbar/dist/perfect-scrollbar.js');
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
$(".letterpic").letterpic();
require('datatables.net/js/jquery.dataTables.js')
require('datatables.net-bs4/css/dataTables.bootstrap4.css');
require('datatables.net-bs4/js/dataTables.bootstrap4.js');
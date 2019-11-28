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
$("[fallback-src]").each(function () {
    this.onerror = function () {
        this.src = this.getAttribute('fallback-src');
    }
});
require('symfony-collection/jquery.collection.js');
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
$('[bootbox-alert]').each(function () {
    $(this).click(function () {
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
$('.symfony-collection').each(function () {
    $(this).collection({
        allow_up: $(this).data('allowUp'),
        allow_down: $(this).data('allowDown'),
        allow_add: $(this).data('allowAdd'),
        allow_remove: $(this).data('allowRemove'),
        allow_duplicate: $(this).data('allowDuplicate'),
        elements_selector: $(this).data('elementsSelector'),
        up: '<a href="#" class="ml-1 mr-1 btn btn-pill btn-secondary"><i class="fa fa-level-up-alt"></i>&nbsp;Subir</a>',
        down: '<a href="#" class="ml-1 mr-1 btn btn-pill btn-secondary"><i class="fa fa-level-down-alt"></i>&nbsp;Bajar</a>',
        add: '<a href="#" class="ml-1 mr-1 btn btn-pill btn-success"><i class="fa fa-plus"></i>&nbsp;Agregar nuevo</a>',
        remove: '<a href="#" class="ml-1 mr-1 btn btn-pill btn-danger"><i class="fa fa-times"></i>&nbsp;Eliminar</a>',
        duplicate: '<a href="#" class="ml-1 mr-1 btn btn-pill btn-warning"><i class="fa fa-clone"></i>&nbsp;Duplicar</a>',
        after_add: function (collection, element) {
            element.find('.text-sync').each(function () {
                var target = $(this).data('target');
                if (target) {
                    $(this).on("keyup paste",function () {
                        switch ($(target).data('syncMode')) {
                            case 'text':
                                $(target).text($(this).val());
                                break;
                            case 'value':
                                $(target).val($(this).val());
                                break;
                            default:

                                break;
                        }
                    });
                }
            });
        },
        init_with_n_elements: 0,
        add_at_the_end: true,
        drag_drop: true,
        fade_in: false,
        fade_out: false
    });

});
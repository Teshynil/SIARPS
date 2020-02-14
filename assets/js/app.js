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
require('daterangepicker/daterangepicker.css');
require('tinymce/skins/ui/oxide/skin.min.css');
require('tinymce/skins/ui/oxide/content.min.css');
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
require('moment');
require('daterangepicker');
window.tinymce = require('tinymce/tinymce');
require('../js/vendor-custom/tinymce/langs/es_MX.js');
require('tinymce/themes/silver');
require('tinymce/plugins/table/plugin.js');
require('tinymce/plugins/quickbars/plugin.js');
require('tinymce/plugins/paste/plugin.js');
require('tinymce/plugins/searchreplace/plugin.js');
require('tinymce/plugins/autolink/plugin.js');
require('tinymce/plugins/code/plugin.js');
require('tinymce/plugins/visualchars/plugin.js');
require('tinymce/plugins/link/plugin.js');
require('tinymce/plugins/charmap/plugin.js');
require('tinymce/plugins/hr/plugin.js');
require('tinymce/plugins/advlist/plugin.js');
require('tinymce/plugins/lists/plugin.js');
require('tinymce/plugins/textpattern/plugin.js');

const bsCustomFileInput = require('bs-custom-file-input/dist/bs-custom-file-input.js')
global.bsCustomFileInput = bsCustomFileInput;
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
$(document).ready(function () {
    bsCustomFileInput.init();
});
$(".letterpic").letterpic();
var daterangepickerLocale = {
    "format": "DD/MM/YYYY",
    "separator": " - ",
    "applyLabel": "Aplicar",
    "cancelLabel": "Cancelar",
    "fromLabel": "Desde",
    "toLabel": "Hasta",
    "customRangeLabel": "Personalizado",
    "weekLabel": "S",
    "daysOfWeek": [
        "Do",
        "Lu",
        "Ma",
        "Mi",
        "Ju",
        "Vi",
        "Sa"
    ],
    "monthNames": [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
    ],
    "firstDay": 1
};
$('a[soft]').click(function (e) {
    e.preventDefault();
    $.ajax({
        url: this.href,
    }).always(function () {
        location.reload();
    });
});
$('.input-group input[type=range]').each(function () {
    var label = $($(this).parents('.range-input')[0]).find('span.input-group-text');
    var input = $(this);
    label.html(this.value);
    this.oninput = function () {
        label.html(this.value);
    };
});
$("input.datetimepicker").each(function () {
    $(this).before('<div class="picker form-control btn-secondary"><i class="fa fa-calendar"></i>&nbsp;<span></span></div>');
    var picker = $(this).parent().find('div.picker');
    var input = $(this);
    input.hide();
    function cb(start, end) {
        var time = input.data('time') == 'time';
        if (input.data('type') == 'simple') {
            picker.find('span').html(start.format('DD/MM/YYYY' + (time ? ' hh:mm' : '')));
            input.val(start.format('DD/MM/YYYY' + (time ? ' hh:mm' : '')));
        } else {
            picker.find('span').html(start.format('DD/MM/YYYY' + (time ? ' hh:mm' : '')) + ' - ' + end.format('DD/MM/YYYY' + (time ? ' hh:mm' : '')));
            input.val(start.format('DD/MM/YYYY' + (time ? ' hh:mm' : '')) + ' - ' + end.format('DD/MM/YYYY' + (time ? ' hh:mm' : '')));
        }
    }
    ;
    picker.daterangepicker({
        "locale": daterangepickerLocale,
        "singleDatePicker": $(this).data('type') == 'simple' ? true : false,
        "timePicker": $(this).data('time') == 'time' ? true : false,
        "showDropdowns": true,
        "startDate": new Date(),
        "endDate": new Date(),
        "timePicker24Hour": true,
        "minDate": $(this).data('minDate') ? $(this).data('minDate') : "01/01/2000",
        "maxDate": $(this).data('maxDate') ? $(this).data('maxDate') : "12/31/2099"
    }, cb);
});
$('.symfony-collection').each(function () {
    $(this).collection({
        allow_up: $(this).data('allowUp'),
        allow_down: $(this).data('allowDown'),
        allow_add: $(this).data('allowAdd'),
        allow_remove: $(this).data('allowRemove'),
        allow_duplicate: $(this).data('allowDuplicate'),
        elements_selector: $(this).data('elementsSelector'),
        up: '<a href="#" class="m-1 btn btn-pill btn-secondary"><i class="fa fa-level-up-alt"></i>'
                + ($(this).data('minButtons') !== null ? '' : '&nbsp;Subir') + '</a>',
        down: '<a href="#" class="m-1 btn btn-pill btn-secondary"><i class="fa fa-level-down-alt"></i>'
                + ($(this).data('minButtons') !== null ? '' : '&nbsp;Bajar') + '</a>',
        add: '<a href="#" class="m-1 btn btn-pill btn-success"><i class="fa fa-plus"></i>'
                + ($(this).data('minButtons') !== null ? '' : '&nbsp;Agregar nuevo') + '</a>',
        remove: '<a href="#" class="m-1 btn btn-pill btn-danger"><i class="fa fa-times"></i>'
                + ($(this).data('minButtons') !== null ? '' : '&nbsp;Eliminar') + '</a>',
        duplicate: '<a href="#" class="m-1 btn btn-pill btn-warning"><i class="fa fa-clone"></i>'
                + ($(this).data('minButtons') !== null ? '' : '&nbsp;Duplicar') + '</a>',
        after_add: function (collection, element) {
            element.find('.text-sync').each(function () {
                var target = $(this).data('target');
                if (target) {
                    $(this).on("keyup paste", function () {
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
tinymce.init({
    selector: 'textarea.wysiwyg',
    language: 'es_MX',
    plugins: 'paste searchreplace autolink code visualchars link table charmap hr advlist lists textpattern quickbars',
    menubar: 'edit insert format table',
    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | charmap | link',
    toolbar_sticky: true,
    height: 400,
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic underline | quicklink',
    quickbars_insert_toolbar: '',
    toolbar_drawer: 'sliding',
    contextmenu: "cut copy paste link table",
});
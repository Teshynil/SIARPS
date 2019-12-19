require('codemirror/lib/codemirror.css');
const CodeMirror = require('codemirror');
global.CodeMirror = global.CodeMirror = CodeMirror;
require('codemirror/mode/yaml/yaml.js');
$('#form-fields').hide();
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
            element.find('.code-editor').each(function () {
                var code = CodeMirror.fromTextArea(this, {
                    lineNumbers: true,
                    mode: 'yaml'
                });
                var textarea = this;
                code.on('change', function (i) {
                    textarea.value = i.getValue().replace(/\t/g, "    ");
                });
            });
        },
        init_with_n_elements: 0,
        add_at_the_end: true,
        drag_drop: true,
        fade_in: false,
        fade_out: false
    });

});

$('.type-field-collapser').each(function () {
    if (this.value == "Form") {
        $('#form-fields').show();
    } else {
        $('#form-fields').hide();
    }
    $(this).change(function () {
        if (this.value == "Form") {
            $('#form-fields').show();
        } else {
            $('#form-fields').hide();
        }
    });

});
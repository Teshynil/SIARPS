require('codemirror/lib/codemirror.css');
require('../../css/print.css');
//JS
const CodeMirror = require('codemirror');
global.CodeMirror = global.CodeMirror = CodeMirror;
require('codemirror/mode/twig/twig.js');
require('codemirror/mode/htmlmixed/htmlmixed.js');
require('codemirror/addon/mode/overlay.js');

CodeMirror.defineMode("htmltwig", function (config, parserConfig) {
    return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), CodeMirror.getMode(config, "twig"));
});

const $ = require('jquery');
global.$ = global.jQuery = $;
$('.code-editor').each(function () {
    var code = CodeMirror.fromTextArea(this, {
        lineNumbers: true,
        mode: 'htmltwig'
    });
    var textarea = this;
    code.on('change', function (i) {
        textarea.value = i.getValue();
    });
});
var header = $('.header');
var footer = $('.footer');
$('.page').each(function () {
    $(this).children().wrapAll($('<div class="page-body"></div>'));
    if (header !== null) {
        $(this).prepend(header.clone());
    }
    if (footer !== null) {
        $(this).append(footer.clone());
    }
});
if (header !== null) {
    header.remove();
}
if (footer !== null) {
    footer.remove();
}
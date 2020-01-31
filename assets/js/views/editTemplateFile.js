require('codemirror/lib/codemirror.css');
require('codemirror/addon/fold/foldgutter.css');
require('../../css/print.css');
//JS
const CodeMirror = require('codemirror');
global.CodeMirror = global.CodeMirror = CodeMirror;
require('codemirror/mode/twig/twig.js');
require('codemirror/mode/htmlmixed/htmlmixed.js');
require('codemirror/addon/mode/overlay.js');
require('codemirror/addon/fold/foldcode.js');
require('codemirror/addon/fold/foldgutter.js');
require('codemirror/addon/fold/brace-fold.js');
require('codemirror/addon/fold/xml-fold.js');
require('codemirror/addon/fold/indent-fold.js');
require('codemirror/addon/fold/markdown-fold.js');
require('codemirror/addon/fold/comment-fold.js');

CodeMirror.defineMode("htmltwig", function (config, parserConfig) {
    return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), CodeMirror.getMode(config, "twig"));
});

const $ = require('jquery');
global.$ = global.jQuery = $;
$('.code-editor').each(function () {
    var code = CodeMirror.fromTextArea(this, {
        mode: 'htmltwig',
        lineNumbers: true,
        extraKeys: {"Ctrl-Q": function (cm) {
                cm.foldCode(cm.getCursor());
            }},
        foldGutter: true,
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
    });
    var textarea = this;
    code.on('change', function (i) {
        textarea.value = i.getValue();
    });
});
var header = $('.header');
var footer = $('.footer');
$('.page').each(function () {
    $(this).wrapInner($('<div class="page-body"></div>'));
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
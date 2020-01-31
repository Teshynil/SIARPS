require('../../css/print.css');
//JS

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
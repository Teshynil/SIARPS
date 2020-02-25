require('../../css/print.css');
//JS

var header = $('.header');
var footer = $('.footer');
$('.page').each(function () {
    $(this).wrapInner($('<div class="page-body"></div>'));
    if (header[0] != null) {
        $(this).prepend(header.clone());
    }
    if (footer[0] != null) {
        $(this).append(footer.clone());
    }
});
if (header[0] != null) {
    header[0].setAttribute('class', 'd-none');
    header[0].id = "header-prototype"
}
if (footer[0] != null) {
    footer[0].setAttribute('class', 'd-none');
    footer[0].id = "footer-prototype"
}

const getOffset = (element) => {
    if (!element)
        return 0;
    return getOffset(element.offsetParent) + (element.offsetTop);
}
const getNextOffset = (element) => {
    return getOffset(element) + element.clientHeight;
}

const indexesOf = (string, regex) => {
    var match,
            indexes = {};

    regex = new RegExp(regex);

    while (match = regex.exec(string)) {
        if (!indexes[match[0]])
            indexes[match[0]] = [];
        indexes[match[0]].push(match.index);
    }

    return indexes;
}


$(window).on("load", function () {
    do {
        $('.page-body:not([processed])').each(function () {
            var pageParent = this.parentElement;
            var page = this;
            var pageMin = getOffset(page);
            var pageMax = pageMin + page.clientHeight;
            for (let child of page.children) {
                if (getNextOffset(child) > pageMax) { //Overflow element
                    if (child.tagName == "TABLE") {
                        var thead = $('> thead', child)[0];
                        var tbody = $('> tbody', child)[0];
                        var pagebreak = false;

                        var header = $('#header-prototype').clone();
                        if (header[0] != null) {
                            header[0].id = "";
                            header[0].className = "header"
                        }
                        var footer = $('#footer-prototype').clone();
                        if (footer[0] != null) {
                            footer[0].id = "";
                            footer[0].className = "footer";
                        }
                        var subpage = $('<div class="page sub-page"></div>');
                        var subpagebody = $('<div class="page-body"></div>');
                        var table = $('<table></table>');
                        var tableBody = $('<tbody></tbody>');
                        for (var irow = 0; irow < tbody.childElementCount; irow++) {
                            var row = tbody.children[irow];
                            var rowSize = 1;
                            for (let cell of row.children) {
                                if (cell.rowSpan > rowSize) {
                                    rowSize = cell.rowSpan;
                                }
                            }
                            var max = getNextOffset(tbody.children[irow + rowSize - 1]);
                            var size = max - getOffset(tbody.children[irow]);
                            if (!pagebreak && max > pageMax) { //page-break point
                                pagebreak = true;
                                if (size >= (page.clientHeight - (thead ? thead.clientHeight : 0))) { //mid-row break point
                                    pagebreak = false;
                                    irow += rowSize - 1;
                                    continue;
                                    //not working
                                    for (let rcell of row.children) {
                                        $(rcell).find('span').each(function () {
                                            if (getNextOffset(this) > pageMax) {
                                                this.normalize();
                                                for (var inode = 0; inode < this.childNodes.length; inode++) {
                                                    var node = this.childNodes[inode];
                                                    if (node.nodeType == document.TEXT_NODE) {
                                                        var indexes = indexesOf(node.wholeText, /\s/g);
                                                        var sum = 0;
                                                        var nextNode = node;
                                                        for (var i=0;i<indexes.length;i++) {
                                                            var idx=indexes[i];
                                                            nextNode = nextNode.splitText(idx - sum);
                                                            sum += idx;
                                                        }
                                                    } else {

                                                    }
                                                }
                                            }
                                        });
                                    }
                                }
                                table.append($(thead).clone());
                                table.append(tableBody);
                            }
                            if (pagebreak) {
                                for (var trow = irow; trow < irow + rowSize; trow++) {
                                    tableBody.append($(tbody.children[trow]).clone());
                                    $(tbody.children[trow]).attr('remove', true);
                                }
                                irow += rowSize - 1;
                            }
                        }
                        if (pagebreak) {
                            $('[remove]').remove();
                            subpagebody.append(table);
                            subpage.append(subpagebody);
                            subpage.prepend(header);
                            subpage.append(footer);
                            $(pageParent).after(subpage);
                        }
                    }
                }
            }
            $(page).attr('processed', true);
        });
    } while ($('.page-body:not([processed])').length > 0);
});
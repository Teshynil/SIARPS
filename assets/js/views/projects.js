$('.datatable').DataTable({
    initComplete: function () {
        this.api().rows().every(function () {
            var row = this;
            if (row.node().getAttribute('href') !== null) {
                var link = $('<a style="display:contents;" href="' + row.node().getAttribute('href') + '">');
                var node = $(row.node());
                var children = node.children();
                node.append(link);
                children.appendTo(link);
            }
        });
    }
});
$('.datatable').attr('style', 'border-collapse: collapse !important');
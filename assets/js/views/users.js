$('.datatable').DataTable({
    initComplete: function() {
        this.api().columns().every(function() {
            var column = this;
            if ($(column.header()).data("columnFilter") !== undefined) {
                var select = $('<select class="form-control"><option value="">(Todo)</option></select>');
                var header = $($(column.header()).parents()[1].firstElementChild.childNodes[column.index()]).empty();
                select.appendTo(header);
                select.on('change', function() {
                    var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                            );

                    column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                });

                column.data().unique().sort().each(function(d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            }
        });
    }
});
$('.datatable').attr('style', 'border-collapse: collapse !important');
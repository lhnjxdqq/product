function tableColumn (obj) {
    var th      = $(obj.selector).find('thead tr th'),
        table   = '<div class="table-responsive"><table class="table table-hover table-bordered"><tr>';
    $.each(th, function (index, val) {
        var cellIndex   = val.cellIndex,
            cellText    = val.innerText;
        table += '<td><input type="checkbox" checked data-column="' + cellIndex + '"> ' + cellText + '</td>';
    });
    table += '</tr></table></div>';
    $(obj.container).append(table).delegate('table tr td input', 'click', function () {
        var column = obj.dataTable.column( $(this).attr('data-column') );
        column.visible( ! column.visible() );
    });
}
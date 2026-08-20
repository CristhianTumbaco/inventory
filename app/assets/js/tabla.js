function formatDatatable(id, item, order) {
    if (!$(id).length) return;
    const inputId = '#' + id.replace('#', '') + 'buscador';
    $(inputId).val('');
    $(id).DataTable().destroy();
    const localTable = $(id).DataTable({
        paging: true,
        ordering: true,
        info: true,
        searching: true,
        searchHighlight: true,
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Ver _MENU_",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Sin Datos",
            sInfo: "_START_ al _END_ de _TOTAL_ registros",
            sInfoEmpty: "0",
            sInfoFiltered: "(total _MAX_ )",
            sLoadingRecords: "Cargando..."
        },
        order: [item, order]
    });
    $('.dataTables_filter').css('display', 'none');
    $('.dt-search').css('display', 'none');
    $(inputId).off('keyup').on('keyup', function () {
        localTable.search(this.value).draw();
    });
    $('.dt-length').addClass('d-none');
    const footer = document.querySelector(
        '#table_wrapper .dt-layout-table'
    ).nextElementSibling;
    footer.id = 'pagingFooter';
    $('#pagingFooter').removeClass('mt-2').addClass('border mt-0 p-2 rounded-3 rounded-top-0 bg-light').css('margin', '12px');
}

function exportToExcel(jsonData, fileName) {
    var workbook = XLSX.utils.book_new();
    var worksheet = XLSX.utils.json_to_sheet(jsonData);
    XLSX.utils.book_append_sheet(workbook, worksheet, "Hoja 1");
    XLSX.writeFile(workbook, fileName + ".xlsx");
}
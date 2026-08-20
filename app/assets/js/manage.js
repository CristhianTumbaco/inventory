const url = new URL(document.currentScript.src);
const menu = url.searchParams.get('menu');
$('#parametros').addClass('selected');
$('#parametros a').addClass('active');
$('#collapseExample').addClass('show');
$(`#${menu}`).addClass('selected');
$(`#${menu} a`).addClass('active');

if ($('#table').length > 0) {
    formatDatatable('table', 0, 'asc');
}
const sidebar = document.getElementById('sidebarnav');
const activeItem = sidebar.querySelector('.active');

if (activeItem) {
    activeItem.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}
$(document).ready(function () {
  //activar tabs del menu
  $('#dashboard').addClass('selected');
  $('#dashboard a').addClass('active');
  dropdownFormat();
  initView();
});
function initView() {
  fetchAnalytics();
}
const commonsFilters = {
  period: 'month',
  dateFrom: null,
  dateTo: null,
  module: 'inventory'
};
function dropdownFormat() {
  const moduloSeleccionado = document.getElementById('moduloSeleccionado');
  const periodoSeleccionado = document.getElementById('periodoSeleccionado');
  const rangoPersonalizado = document.getElementById('rangoPersonalizado');
  if (!periodoSeleccionado) return;
  if (!moduloSeleccionado) return;
  document.querySelectorAll('.periodo-item').forEach(item => {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      const value = this.dataset.value;
      const text = this.querySelector('.ms-2').textContent;
      if (value === 'custom') {
        rangoPersonalizado.classList.remove('d-none');
        return;
      }
      commonsFilters.period = value;
      commonsFilters.dateFrom = null;
      commonsFilters.dateTo = null;
      periodoSeleccionado.textContent = text;
      rangoPersonalizado.classList.add('d-none');
      fetchAnalytics();
    });
  });
  document.querySelectorAll('.modulo-item').forEach(item => {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      const value = this.dataset.value;
      const text = this.querySelector('.ms-2').textContent;
      commonsFilters.module = value;
      moduloSeleccionado.textContent = text;
      fetchAnalytics();
    });
  });
  document.getElementById('btnAplicarRango')?.addEventListener('click', function () {
    const desde = document.getElementById('fechaDesde').value;
    const hasta = document.getElementById('fechaHasta').value;
    if (!desde || !hasta) {
      Notify(2, 'Debe seleccionar ambas fechas');
      return;
    }
    commonsFilters.period = 'custom';
    commonsFilters.dateFrom = desde;
    commonsFilters.dateTo = hasta;
    const desdeFormateado = formatearFecha(desde);
    const hastaFormateado = formatearFecha(hasta);
    periodoSeleccionado.textContent = `${desdeFormateado} - ${hastaFormateado}`;
    rangoPersonalizado.classList.add('d-none');
    fetchAnalytics();
  });
}
function formatearFecha(fecha) {

  const [year, month, day] = fecha.split('-');

  return `${day}/${month}/${year}`;
}
function fetchAnalytics() {
  sendAjax({
    endpoint: '/Dashboard/analytics',
    type: 'POST',
    data: commonsFilters,
    beforeSend: function () {
      loadingChart('stats');
    },
    onSuccess: (data) => {
      setTimeout(() => {
        if (data.success) {
          redirectAnalytics(data.data);
        } else {
          Notify(2, data.message);
        }
      }, 1200);
    }
  });
}
function redirectAnalytics(data) {
  if (commonsFilters.module === 'purchase') {
    buildPurchasesStats(data.stats);
    buildPurchasesGraph(data.graphDay);
    buildPurchasesGraphSupplier(data.graphSupplier);
    buildPurchasesGraphProducts(data.graphProducts);
    buildPurchasesGraphTaxes(data.graphTaxes);
  } else if (commonsFilters.module === 'sales') {
    buildSalesStats(data.stats);
    buildSalesGraph(data.graphDay);
    buildSalesGraphSupplier(data.graphSupplier);
    buildSaleGraphProducts(data.graphProducts);
    buildSalesGraphTaxes(data.graphTaxes);
  } else if (commonsFilters.module === 'inventory') {
    buildInventoryStats(data.stats);
    buildInventoryGraph(data.graphDay);
    buildInventoryGraphTotal(data.graphTotal);
    buildInventoryGraphLocation(data.graphLocation);
    buildInventoryGraphProducts(data.graphProducts);
    buildInventoryDataExpired(data.dataExpired);
  }
}



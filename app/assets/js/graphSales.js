

function buildSalesStats(data) {
  const current = data.current;
  const previous = data.previous;
  const totalUp = Number(current.total) > Number(previous.total);
  const totalColor = totalUp ? 'text-success' : 'text-danger';
  const totalIcon = totalUp ? 'arrow_upward_alt' : 'arrow_downward_alt';
  const cantidadUp = Number(current.cantidad) > Number(previous.cantidad);
  const cantidadColor = cantidadUp ? 'text-success' : 'text-danger';
  const cantidadIcon = cantidadUp ? 'arrow_upward_alt' : 'arrow_downward_alt';
  const cantidadValue = Number(current.cantidad) - Number(previous.cantidad);

  let html = `<div class="row">
    <div class="col-lg-8 d-flex align-items-strech">
        <div class="card w-100 mb-2">
            <div class="card-body p-0">
                <div id="chart1" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="col-lg-12 mb-2">
                <div class="card shadow border card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-success-subtle rounded-circle p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-success" style="font-size: 2rem;">paid</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h6 mb-1">Total Vendido</div>
                            <div class="fw-semibold h4 mb-1">$${Number(current.total).toLocaleString('en-US', { minimumFractionDigits: 2 })}</div>
                            <div class="text-muted d-flex align-items-center">
                                <div class="d-flex align-items-center ${totalColor}">
                                    <span class="material-symbols-outlined">${totalIcon}</span>
                                    <span class="fs-2">${Number(previous.percentage)}%</span>
                                </div>
                                <span class="ms-2 fs-2 text-muted">vs periodo anterior</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-2">
                <div class="card shadow border card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-primary-subtle rounded-circle p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-primary" style="font-size: 2rem;">shopping_bag</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h6 mb-1">N° de Ventas</div>
                            <div class="fw-semibold h4 mb-1">${current.cantidad}</div>
                            <div class="text-muted d-flex align-items-center">
                                <div class="d-flex align-items-center ${cantidadColor}">
                                    <span class="material-symbols-outlined">${cantidadIcon}</span>
                                    <span class="fs-2">${cantidadValue}</span>
                                </div>
                                <span class="ms-2 fs-2 text-muted">vs periodo anterior</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-2">
              <div class="card w-100">
                <div class="card-body p-0">
                      <div id="chart4"></div>
                  </div>
              </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 d-flex align-items-strech mb-2">
        <div class="card w-100">
            <div class="card-body p-0">
                <div id="chart2" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 d-flex align-items-strech mb-2">
        <div class="card w-100">
            <div class="card-body p-0">
                <div id="chart3" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>`;
  $('#stats').empty().html(html);
}
function buildSalesGraph(data) {

  const categories = data.map(item => {
    const [year, month, day] = item.date.split('-');
    return `${day}/${month}`;
  });


  const options = {
    series: [{
      name: '',
      data: data.map(item => Number(item.total))
    }],
    chart: {
      type: 'bar',
      height: 400,
      toolbar: {
        show: false
      }
    },
    title: {
      text: 'Ventas por día',
      align: 'left',
    },
    colors: ['#1a2e56'],
    xaxis: {
      categories: categories
    },
    yaxis: {
      labels: {
        formatter: function (value) {
          return '$' + value.toLocaleString();
        }
      }
    },
    tooltip: {
      marker: {
        show: false
      },
      y: {
        formatter: function (value, { dataPointIndex }) {
          const item = data[dataPointIndex];
          return `Total: <span class="text-primary">$${Number(item.total).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
          <br>
          Impuestos: <span class="text-danger">$${Number(item.impuestos).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
          <br>
          Ventas: ${item.cantidad}`;
        }
      }
    }
  };
  const chart = new ApexCharts(
    document.querySelector("#chart1"),
    options
  );
  chart.render();
}
function buildSalesGraphSupplier(data) {

  const categories = data.map(item => item.name);
  const totals = data.map(item => Number(item.total));

  const options = {
    series: [{
      name: 'Total',
      data: totals
    }],
    chart: {
      type: 'bar',
      height: 300,
      toolbar: {
        show: false
      }
    },
    title: {
      text: 'Ventas por proveedor',
      align: 'left',
    },
    plotOptions: {
      bar: {
        horizontal: true,
        borderRadius: 4,
        borderRadiusApplication: 'end',
      }
    },
    colors: ['#14b8a6'],
    xaxis: {
      categories: categories
    },
    tooltip: {
      y: {
        formatter: function (value) {
          return '$' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        title: {
          formatter: () => ''
        }
      }
    }
  };

  const chart = new ApexCharts(
    document.querySelector('#chart2'),
    options
  );

  chart.render();
}
function buildSaleGraphProducts(data) {
  const categories = data.map(item => {
    const words = item.name.split(' ');
    if (item.name.length <= 20) {
      return item.name;
    }
    const middle = Math.ceil(words.length / 2);
    return [
      words.slice(0, middle).join(' '),
      words.slice(middle).join(' ')
    ];
  });
  const totals = data.map(item => Number(item.total));

  const options = {
    series: [{
      name: 'Total',
      data: totals
    }],
    chart: {
      type: 'bar',
      height: 300,
      toolbar: {
        show: false
      }
    },
    title: {
      text: 'Productos más vendidos (LB)',
      align: 'left',
    },
    colors: ['#1565C0'],
    plotOptions: {
      bar: {
        borderRadius: 4,
        borderRadiusApplication: 'end',
        horizontal: true
      }
    },
    xaxis: {
      categories: categories
    },
    tooltip: {
      y: {
        formatter: function (value) {
          return Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' LB';
        },
        title: {
          formatter: () => ''
        }
      }
    }
  };

  const chart = new ApexCharts(
    document.querySelector('#chart3'),
    options
  );
  chart.render();
}

function buildSalesGraphTaxes(data) {
  const options = {
    series: [
      Number(data.total),
      Number(data.taxes)
    ],

    chart: {
      type: 'donut',
      height: 150
    },

    labels: [
      'Ventas',
      'Impuestos'
    ],
    colors: ['#0EA5E9', '#F43F5E'],
    plotOptions: {
      pie: {
        borderRadius: 12,
        spacing: 5,
        donut: {
          size: '68%',
          labels: {
            show: true,
            value: {
              fontSize: '11px'
            },
            total: {
              show: true,
              fontSize: '11px',
              label: 'Impuestos',
              formatter: function () {
                return `${(Number(data.taxes))}%`;
              }
            }
          }
        }
      }
    },
    dataLabels: {
      enabled: false
    },

    tooltip: {
      y: {
        formatter: function (value) {
          return `${value.toFixed(2)}%`;
        }
      }
    },

    legend: {
      position: 'bottom'
    }
  };

  const chart = new ApexCharts(
    document.querySelector('#chart4'),
    options
  );

  chart.render();
}
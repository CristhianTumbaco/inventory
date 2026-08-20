function buildInventoryStats(data) {
    const stock = data.stock;
    const inputOutput = data.inOut;
    const expired = data.expired;
    const temperature = data.temperature;
    let aditional = '';
    temperature.forEach(item => {
        aditional += `
        <div class="col-lg-3 mb-2">
            <div class="d-flex align-items-center">
                <span class="material-symbols-outlined text-info">ac_unit</span>
                <div>
                    <div class="fw-bold">${item.name} (${Number(item.temperature)} °C)</div>
                    <div class="text-muted fs-2">
                        Ult. actualización: ${timeAgo(item.update_temp)}
                    </div>
                </div>
            </div>
        </div>
    `;
    });
    let html = `<div class="row">
    <div class="col-lg-3 mb-2">
        <div class="card shadow border card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3 p-2">
                <div class="bg-info-subtle rounded-circle p-3 d-flex justify-content-center align-items-center">
                    <span class="material-symbols-outlined text-info" style="font-size: 2rem;">inventory</span>
                </div>
                <div>
                    <div class="text-muted fw-bold h6 mb-1">STOCK</div>
                    <div class="fw-semibold h4 mb-1">${Number(stock.total).toLocaleString('en-US', { minimumFractionDigits: 2 })} LB</div>
                    <div class="text-muted d-flex align-items-center">
                        <span class="material-symbols-outlined">package_2</span>
                        <span class="ms-2 fs-2 text-muted">${Number(stock.package)} paquetes disponibles</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-2">
        <div class="card shadow border card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3 p-2">
                <div class="bg-success-subtle rounded-circle p-3 d-flex justify-content-center align-items-center">
                    <span class="material-symbols-outlined text-success" style="font-size: 2rem;">arrow_downward</span>
                </div>
                <div>
                    <div class="text-muted fw-bold h6 mb-1">ENTRADAS</div>
                    <div class="fw-semibold h4 mb-1">${Number(inputOutput.entrada.total).toLocaleString('en-US', { minimumFractionDigits: 2 })} LB</div>
                    <div class="text-muted d-flex align-items-center">
                        <span class="material-symbols-outlined">package_2</span>
                        <span class="ms-2 fs-2 text-muted">${Number(inputOutput.entrada.package)} paquetes disponibles</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-2">
        <div class="card shadow border card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3 p-2">
                <div class="bg-primary-subtle rounded-circle p-3 d-flex justify-content-center align-items-center">
                    <span class="material-symbols-outlined text-primary" style="font-size: 2rem;">arrow_left_alt</span>
                </div>
                <div>
                    <div class="text-muted fw-bold h6 mb-1">SALIDAS</div>
                    <div class="fw-semibold h4 mb-1">${Number(inputOutput.salida.total).toLocaleString('en-US', { minimumFractionDigits: 2 })} LB</div>
                    <div class="text-muted d-flex align-items-center">
                        <span class="material-symbols-outlined">package_2</span>
                        <span class="ms-2 fs-2 text-muted">${Number(inputOutput.salida.package)} paquetes vendidos</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-2">
        <div class="card shadow border card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3 p-2">
                <div class="bg-danger-subtle rounded-circle p-3 d-flex justify-content-center align-items-center">
                    <span class="material-symbols-outlined text-danger" style="font-size: 2rem;">warning</span>
                </div>
                <div>
                    <div class="text-muted fw-bold h6 mb-1">POR VENCER</div>
                    <div class="fw-semibold h4 mb-1">${Number(expired.total)}</div>
                    <span class="fs-2 text-muted">Menos de 30 días</span>
                </div>
            </div>
        </div>
    </div>
    ${aditional}
</div>
<div class="row">
    <div class="col-lg-8 col-12">
        <div class="card w-100 mb-2">
            <div class="card-body p-0">
                <div id="chart1" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12">
        <div class="card w-100 mb-2">
            <div class="card-body p-0">
                <div id="chart4" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12">
        <div class="card w-100 mb-2">
            <div class="card-body p-0">
                <div id="chart2" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-12">
        <div class="card w-100 mb-2">
            <div class="card-body p-0">
                <div id="chart3" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-12">
        <div class="card w-100 mb-2" id="dataExpired">
        </div>
    </div>
</div>`;
    $('#stats').empty().html(html);
}
function buildInventoryGraph(data) {

    const categories = data.map(item => {
        const [year, month, day] = item.fecha.split('-');
        return `${day}/${month}`;
    });


    const options = {
        series: [
            {
                name: 'Compras',
                data: data.map(item => Number(item.entradas))
            },
            {
                name: 'Ventas',
                data: data.map(item => Number(item.salidas))
            },
            {
                name: 'Producción',
                data: data.map(item => Number(item.produccion))
            },
            {
                name: 'Merma',
                data: data.map(item => Number(item.merma))
            }
        ],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            }
        },
        title: {
            text: 'Flujo del inventario por día',
            align: 'left',
        },
        xaxis: {
            categories: categories
        },
        colors: ['#16deb9', '#5d87ff', '#6851d7', '#f70167'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 0
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    if (value) {
                        return value.toLocaleString() + ' LB';
                    } else {
                        return 0;
                    }

                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false
        }
    };
    const chart = new ApexCharts(
        document.querySelector("#chart1"),
        options
    );
    chart.render();
}
function buildInventoryGraphLocation(data) {
    const categories = data.map(item => item.name);
    const totals = data.map(item => Number(item.total));

    const options = {
        series: [{
            name: 'Ubicaciones',
            data: totals
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        title: {
            text: 'Stock por ubicación',
            align: 'left',
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4,
                borderRadiusApplication: 'end',
            }
        },
        colors: ['#1a2e56'],
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
        document.querySelector('#chart2'),
        options
    );
    chart.render();
}
function buildInventoryGraphProducts(data) {
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
    const totals = data.map(item =>
        Number((Number(item.total) / 2200).toFixed(2))
    );
    const options = {
        series: [{
            name: 'Stock',
            data: totals
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        title: {
            text: 'Stock por producto',
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
            categories: categories,
            labels: {
                formatter: function (value) {
                    return Number(value) + 'T';
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    const toneladas = Number(value);
                    const libras = toneladas * 2200;
                    return `
                        ${libras.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })} Libras
                        <br>
                        ${toneladas.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })} Toneladas
                    `;
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
function buildInventoryGraphTotal(data) {
    const options = {
        series: [
            Number(data.entradas),
            Number(data.salidas),
            Number(data.produccion),
            Number(data.merma)
        ],

        chart: {
            type: 'pie',
            height: 350
        },

        labels: [
            'Compras',
            'Ventas',
            'Producción',
            'Merma',

        ],
        colors: ['#16deb9', '#5d87ff', '#6851d7', '#f70167'],
        title: {
            text: 'Resumen de inventario',
            align: 'left',
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    external: {
                        show: true,
                    },
                },
            },
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' LB';
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
function buildInventoryDataExpired(data) {
    let rows = '';
    data.forEach(item => {
        rows += `<tr>
        <td class="fw-semibold text-primary">${item.batch_code}</td>
        <td>${item.producto}</td>
        <td>${item.subproducto}</td>
        <td>${item.expiration_date}</td>
        <td>${item.dias} días</td>
        </tr>`;
    });
    let html = `
    <div class="card-header">
        <div class="mb-2 fw-semibold h5 text-dark">Proximos a expirar</div>
    </div>
    <div class="card-body p-2 table-responsive">
        <table class="table table-sm table-borderless">
            <thead>
                <tr>
                <th>Lote</th>
                <th>Producto</th>
                <th>Subproducto</th>
                <th>Expiración</th>
                <th>Expira en</th>
                </tr>
            </thead>
            <tbody>
            ${rows}
            </tbody>
        </table>
    </div>`;
    $('#dataExpired').empty().html(html);
}
function timeAgo(date) {
    const now = new Date();
    const past = new Date(date.replace(' ', 'T'));

    const seconds = Math.floor((now - past) / 1000);

    if (seconds < 60) {
        return `hace ${seconds} sg`;
    }

    const minutes = Math.floor(seconds / 60);

    if (minutes < 60) {
        return `hace ${minutes} min`;
    }

    const hours = Math.floor(minutes / 60);

    if (hours < 24) {
        return `hace ${hours} h`;
    }

    const days = Math.floor(hours / 24);

    if (days < 30) {
        return `hace ${days} ${days === 1 ? 'día' : 'días'}`;
    }

    const months = Math.floor(days / 30);

    if (months < 12) {
        return `hace ${months} ${months === 1 ? 'mes' : 'meses'}`;
    }

    const years = Math.floor(days / 365);

    return `hace ${years} ${years === 1 ? 'año' : 'años'}`;
}
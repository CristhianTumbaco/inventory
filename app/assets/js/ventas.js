let catalogData = [];
let productData = [];
let productionInputs = [];
$(document).ready(function () {
    //activar tabs del menu
    $('#ventas').addClass('selected');
    $('#ventas a').addClass('active');

    //proceso compras
    bindButtonClick('.btn-createProduct', createProduct);
    bindButtonClick('.btn-sale', manageSale);
    bindButtonClick('.btn-createSale', createSale);

    //proceso proveedores
    if ($('.btn-supplier').length) {
        $('#oculto').empty();
        bindButtonClick('.btn-supplier', modalSupplier);
        bindButtonClick('.btn-createSupplier', createSupplier);
    }

    //funciones
    if ($('#subtotal').length) {
        $('#subtotal, #taxes').on('input', calcularTotal);
    }
});


//PROCESO COMPRA
function createProduct(id) {
    sendAjax({
        endpoint: '/Produccion/catalogs',
        beforeSend: function () {
            modalGeneric('modalProduct', 'btn-sale', 'Agregar Productos', 'Seleccione los productos para la venta.', 'deployed_code', 'modal-fullscreen');
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                catalogData = datos['categories'];
                buildCardProducto(datos);
                onProductChange();
            } else {
                Notify(2, data.message);
            }
        }
    });
}
function modalGeneric(id, action, title, subtitle, icon, size) {
    const $modal = buildModalGeneric(id, action, title, subtitle, icon);
    $('#modal').empty().append($modal);
    $('#' + id + ' .modal-dialog').addClass(size);
    $('#' + id + ' .modal-body').attr('id', 'bodygroup');
    $('#' + id).modal('show');
}
function buildCardProducto(data) {
    const $selectProduct = $('<select>').addClass('form-select').attr({ id: 'productId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectSubproduct = $('<select>').addClass('form-select').attr({ id: 'subproductId', required: true }).on('change', function () { list(); }).append($('<option>').val('').text('Seleccione...'));
    data['categories'].forEach(product => {
        $selectProduct.append(
            $('<option>').attr({ 'data-category': product.category_id }).val(product.id).text(product.name)
        );
    });
    const $body = $('<div>').append(
        $('<div>').addClass().addClass('was-validated row mb-3').append(
            $('<div>').addClass('mb-2 col-lg-6 col-12').append(
                $('<label>')
                    .addClass('form-label fw-bold')
                    .append(
                        $('<span>').text('Producto '),
                        $('<span>').addClass('text-danger').text('*')
                    ),
                $('<div>').addClass('input-group view-product').append(
                    $selectProduct
                )
            ),
            $('<div>').addClass('mb-2 col-lg-6 col-12').append(
                $('<label>')
                    .addClass('form-label fw-bold')
                    .append(
                        $('<span>').text('Subproducto '),
                        $('<span>').addClass('text-danger').text('*')
                    ),
                $('<div>').addClass('input-group view-subproduct').append(
                    $selectSubproduct
                )
            ),
        ),
        $('<div>').addClass('table-responsive border rounded-3').append(
            $('<table>').addClass('table table-hover table-sm m-0').append(
                $('<thead>').addClass('table-light').append(
                    $('<tr>').append(
                        $('<th>').addClass('p-2').text('Lote'),
                        $('<th>').addClass('p-2').text('Ubicación'),
                        $('<th>').addClass('p-2').text('Paquetes'),
                        $('<th>').addClass('p-2').text('Peso por Paquete'),
                        $('<th>').addClass('p-2').text('Peso Total'),
                        $('<th>').addClass('p-2').text('Opciones de Venta')
                    )
                ),
                $('<tbody>').attr('id', 'dataMaterial')
            )
        )
    );
    $('#bodygroup').empty().append($body);
}
function list() {
    const subproductId = $('#subproductId').val().trim();
    if (subproductId != '') {
        const data = {
            'subproductId': subproductId
        };
        sendAjax({
            endpoint: '/Stock/production',
            type: 'POST',
            data,
            beforeSend: function () {
            },
            onSuccess: (data) => {
                if (data.success) {
                    buildListProduct(data.data);
                } else {
                    Notify(2, data.message);
                }
            }
        });
    }
}
function buildListProduct(data) {
    let tbody = '';
    if (data.length > 0) {
        data.forEach(item => {
            tbody += `
            <tr class="text-nowrap" data-id="${item.id}" data-batch="${item.batchId}" data-packages="${Number(item.package_count)}">
                <td>${item.batch_code}</td>
                <td>${item.ubicacion}</td>
                <td>${Number(item.package_count) + ' ' + item.presentacion}</td>
                <td>${Number(item.package_weight_lb) + ' Libras'}</td>
                <td>${Number(item.quantity_lb) + ' Libras'}</td>
                <td>
                    <div class="input-group input-group-sm optionSale">
                        <input type="text" onkeyup="calculateTotal('quantity', this)" class="form-control form-control-sm decimal-only quantitySale" placeholder="Cantidad a vender">
                        <div class="money-wrapper">
                            <span class="money-symbol">$</span>
                            <input onkeyup="calculateTotal('price', this)" class="form-control form-control-sm rounded-0 money-input decimal-only priceSale" type="text" placeholder="Precio por libra">
                        </div>
                        <div class="money-wrapper">
                            <span class="money-symbol">$</span>
                            <input onkeyup="calculateTotal('total', this)" class="form-control form-control-sm rounded-0 money-input decimal-only totalSale" type="text" placeholder="Precio total">
                        </div>
                    </div>
                </td>
            </tr>
        `;
        });
    } else {
        tbody = '<tr><td colspan="7" class="text-center">No hay stock del producto seleccionado</td></tr>';
    }
    $('#dataMaterial').empty().html(tbody);
}
function manageSale() {
    const product = $("#productId option:selected").text();
    const categoryId = $("#productId option:selected").data("category");
    const subproduct = $("#subproductId option:selected").text();
    const products = [];
    let hayError = false;
    $('#dataMaterial tr').each(function () {
        const $tr = $(this);
        const inputPackage = Number($tr.find('.quantitySale').val()) || 0;
        const price = Number($tr.find('.priceSale').val()) || 0;
        const total = Number($tr.find('.totalSale').val()) || 0;
        if (inputPackage === 0 && price === 0 && total === 0) {
            return;
        }
        const idPackage = $tr.data('id');
        const batchId = $tr.data('batch');
        const package_weight = $tr.find('td:eq(3)').text().replace(/\D/g, '');
        const quantity = (inputPackage * package_weight).toFixed(2);
        const maxPackage = Number($tr.data('packages'));
        if (inputPackage <= 0 || inputPackage > maxPackage) {
            Notify(2, 'La cantidad ingresada es incorrecta.');
            hayError = true;
            return false;
        }
        if (price <= 0 || total <= 0) {
            Notify(2, 'Ingrese los precios.');
            hayError = true;
            return false;
        }
        products.push({
            id: idPackage,
            categoryId,
            product,
            subproduct,
            batchId,
            batch_code: $tr.find('td:eq(0)').text().trim(),
            ubicacion: $tr.find('td:eq(1)').text().trim(),
            presentacion: $tr.find('td:eq(2)').text().replace(/[0-9]/g, '').trim(),
            package_weight: Number(package_weight),
            inputPackage,
            quantity: Number(quantity),
            price,
            total
        });
    });

    if (hayError) return;
    products.forEach(product => {
        const index = productionInputs.findIndex(item => item.id === product.id);
        if (index !== -1) {
            productionInputs[index] = product;
        } else {
            productionInputs.push(product);
        }
    });
    $('#modalProduct').modal('hide');
    $('#modal').empty();
    buildTableProducts();
    if (products.length > 0) {
        Notify(1, 'Producto agregado.');
    }
}
function buildTableProducts() {
    const $tbody = $('#productsContent');
    if (productionInputs.length === 0) {
        $tbody.empty();
        $('#subtotal').val(0);
        $('#tabproduct').empty().text('0');
        $('#tabquantity').empty().text('0');
        $('#tabsubtotal').empty().text('$0');
        calcularTotal();
        return;
    }
    let totalPurchase = 0;
    let quantityTotal = 0;
    let quantityProduct = 0;
    productionInputs.forEach(product => {
        totalPurchase += parseFloat(product.total);
        quantityTotal += parseFloat(product.quantity);
        quantityProduct++;
    });
    $tbody.empty();
    let cont = 1;
    productionInputs.forEach((product, index) => {
        $tbody.append(
            $('<tr>').attr('data-index', index).append(
                $('<td>').text(cont),
                $('<td>').text(product.product),
                $('<td>').text(product.subproduct),
                $('<td>').text(`${product.inputPackage} ${product.presentacion} de ${product.package_weight} Libras`),
                $('<td>').text(product.quantity),
                $('<td>').addClass('precio').text(product.price),
                $('<td>').addClass('precio').text(product.total),
                $('<td>').addClass('text-center user-select-none').append(
                    $('<span>')
                        .addClass('material-symbols-outlined delete text-danger edit')
                        .text('delete')
                        .attr('title', 'Eliminar')
                )
            )
        );
        cont++;
    });
    $('#tabproduct').empty().text(quantityProduct);
    $('#tabquantity').empty().text(quantityTotal);
    $('#tabsubtotal').empty().text('$' + totalPurchase);
    $('#subtotal').val(totalPurchase.toFixed(2));
    calcularTotal();
}

function createSale(id) {
    const requiredFields = [
        '#supplier',
        '#sale_date',
        '#payment_method',
        '#status'
    ];

    if (!validateRequiredFields(requiredFields)) {
        Notify(2, 'Complete los campos de datos de venta');
        return;
    }
    if (!productionInputs.length > 0) {
        Notify(2, 'Debe agregar productos a la venta.');
        return;
    }
    const data = {
        supplierId: $('#supplier').val(),
        invoice_number: $('#invoice_number').val(),
        sale_date: $('#sale_date').val(),
        subtotal: $('#subtotal').val(),
        taxes: $('#taxes').val(),
        total: $('#total').val(),
        payment_method: $('#payment_method').val(),
        status: $('#status').val(),
        notes: $('#notes').val(),
        products: productionInputs.map(item => ({
            idPackage: item.id,
            categoryId: item.categoryId,
            package_weight: item.package_weight,
            inputPackage: item.inputPackage,
            quantity: item.quantity,
            price: item.price,
            total: item.total
        }))
    }
    sendAjax({
        endpoint: '/Ventas/create',
        type: 'POST',
        data: data,
        beforeSend: function () {
            $('#btncreateSale').prop('disabled', true).empty().append(
                $('<span>').addClass('spinner-border spinner-border-sm').attr({ role: 'status', 'aria-hidden': 'true' }).text(''),
                $('<span>').addClass('ms-2').text('Guardando...')
            );
        },
        onSuccess: (data) => {
            if (data.success) {
                Notify(1, data.message);
                setTimeout(() => {
                    window.location.href = '../Ventas';
                }, 1500);
            } else {
                $('#btncreateSale').prop('disabled', false).empty().append(
                    $('<span>').addClass('material-symbols-outlined').text('check_circle'),
                    $('<span>').addClass('ms-2').text('Registrar Venta')
                );
                Notify(2, data.message);
            }
        }
    });
}

//PROCESO PROVEEDORES
function modalSupplier(id) {
    const idmodal = 'modalSupplier';
    const modal = buildModalGeneric(
        'modalSupplier',
        'btn-createSupplier',
        'Nuevo Proveedor',
        'Registrar un nuevo proveedor',
        'person',
        [
            {
                label: 'Identificación',
                placeholder: 'Ingrese la identificación del proveedor',
                id: 'supplierId',
                type: 'text',
                class: 'form-control'
            },
            {
                label: 'Nombre',
                placeholder: 'Ingrese el nombre del proveedor',
                id: 'supplierName',
                type: 'text',
                class: 'form-control',
                required: true
            },
            {
                label: 'Teléfono',
                placeholder: 'Ingrese el teléfono del proveedor',
                id: 'supplierPhone',
                type: 'text',
                class: 'form-control'
            },
            {
                label: 'Correo',
                placeholder: 'Ingrese el correo del proveedor',
                id: 'supplierEmail',
                type: 'email',
                class: 'form-control'
            },
            {
                label: 'Dirección',
                placeholder: 'Ingrese la dirección del proveedor',
                id: 'supplierAddress',
                type: 'text',
                class: 'form-control'
            }
        ]
    );
    $('#modal').empty().append(modal);
    $('#' + idmodal).modal('show');
}
function createSupplier(id) {
    if ($('#supplierName').val().trim() === '') {
        Notify(2, 'El nombre del proveedor es obligatorio');
        return;
    }
    const data = {
        identification: $('#supplierId').val() || '',
        name: $('#supplierName').val(),
        phone: $('#supplierPhone').val() || '',
        email: $('#supplierEmail').val() || '',
        address: $('#supplierAddress').val() || ''
    };
    sendAjax({
        endpoint: '/Proveedores/create',
        type: 'POST',
        data: data,
        beforeSend: function () {
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                $('#supplier').append(
                    $('<option>')
                        .val(datos.id)
                        .text(datos.name)
                        .prop('selected', true)
                );
                $('#supplier').trigger('change');
                Notify(1, data.message);
                $('#modalSupplier').modal('hide');
            } else {
                Notify(2, data.message);
            }
        }
    });
}


//FUNCIONES
function calcularTotal() {
    const subtotal = parseFloat($('#subtotal').val().replace(',', '.')) || 0;
    const impuestos = parseFloat($('#taxes').val().replace(',', '.')) || 0;
    const total = Math.max(subtotal - impuestos, 0);
    $('#total').val(total.toFixed(2));
}

function onProductChange() {
    $('#productId').on('change', function () {
        const productId = parseInt($(this).val());
        if (!productId) {
            $('.view-subproduct button').addClass('disabled');
        }
        const product = catalogData.find(
            c => c.id === productId
        );
        $('#subproductId')
            .empty()
            .append('<option value="">Seleccione...</option>');

        if (!product) return;
        product.subproducts.forEach(subproduct => {
            $('#subproductId').append(
                $('<option>')
                    .val(subproduct.id)
                    .text(subproduct.name)
            );
        });
        $('.view-subproduct button').removeClass('disabled');
    });
}

function calculateTotal(type, input) {
    const row = input.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantitySale').value) || 0;
    const priceInput = row.querySelector('.priceSale');
    const totalInput = row.querySelector('.totalSale');
    if (quantity <= 0) {
        totalInput.value = '';
        return;
    }
    const price = parseFloat(priceInput.value);
    const total = parseFloat(totalInput.value);
    if (type === 'price') {
        if (!isNaN(price)) {
            totalInput.value = (quantity * price).toFixed(2);
        }
    }
    else if (type === 'total') {
        if (!isNaN(total)) {
            priceInput.value = (total / quantity).toFixed(2);
        }
    }
    else if (type === 'quantity') {
        if (!isNaN(price)) {
            totalInput.value = (quantity * price).toFixed(2);
        } else if (!isNaN(total)) {
            priceInput.value = (total / quantity).toFixed(2);
        }
    }
}
$(document).on('click', '.delete', function () {
    const index = $(this).closest('tr').data('index');
    productionInputs.splice(index, 1);
    buildTableProducts();
});

document.addEventListener('click', async function (e) {
    const item = e.target.closest('.status-item');
    if (!item) return;
    e.preventDefault();
    const dropdown = item.closest('.dropdown');
    const boton = dropdown.querySelector('.dropdown-toggle');
    const id = boton.dataset.id;
    const estadoAnterior = boton.dataset.value;
    const estadoNuevo = item.dataset.value;
    const result = await Swal.fire({
        title: '¿Cambiar estado?',
        text: '¿Está seguro de cambiar el estado?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;
    boton.innerHTML = item.innerHTML;
    boton.dataset.value = estadoNuevo;
    fetchChangeStatus(id, estadoNuevo);
});
function fetchChangeStatus(id, status) {
    sendAjax({
        endpoint: "/Ventas/update",
        type: 'POST',
        data: { id, status },
        onSuccess: (data) => {
            if (data.success) {
                Notify(1, data.message);
            } else {
                Notify(2, data.message);
            }
        }
    });
}



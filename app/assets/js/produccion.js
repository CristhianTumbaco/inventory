let catalogData = [];
let productionInputs = [];
let productionOutputs = [];
let maxAvailableQuantity = 0;
let availableQuantity = 0;
$(document).ready(function () {
    $('#produccion').addClass('selected');
    $('#produccion a').addClass('active');
    bindButtonClick('.btn-prima', modalMaterial);
    bindButtonClick('.btn-material', manageMaterial);
    bindButtonClick('.btn-generated', modalGenerated);
    bindButtonClick('.btn-product', manageProduct);
    bindButtonClick('.btn-create', create);
});
function modalMaterial() {
    sendAjax({
        endpoint: '/Produccion/catalogs',
        beforeSend: function () {
            modalGeneric('modalMaterial', 'btn-material', 'Agregar Materia Prima', 'Seleccione la materia prima a utilizar.', 'deployed_code', 'modal-xl');
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                catalogData = datos['categories'];
                buildCardMaterial(datos);
                onProductChange();
            } else {
                Notify(2, data.message);
            }
        }
    });
}
function modalGenerated() {
    if (availableQuantity > 0) {
        sendAjax({
            endpoint: '/Produccion/catalogsT',
            beforeSend: function () {
                modalGeneric('modalGenerated', 'btn-product', 'Generar Producto', 'Registrar la información del nuevo producto generado.', 'deployed_code', 'modal-fullscreen');
            },
            onSuccess: (data) => {
                if (data.success) {
                    const datos = data.data;
                    catalogData = datos['categories'];
                    buildCardGenerated(datos);
                    onCategoryChangeG();
                    onProductChangeG();
                } else {
                    Notify(2, data.message);
                }
            }
        });
    } else {
        Notify(2, 'Primero debe agregar materia prima');
    }

}
function buildCardMaterial(data) {
    const $selectProduct = $('<select>').addClass('form-select').attr({ id: 'productId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectSubproduct = $('<select>').addClass('form-select').attr({ id: 'subproductId', required: true }).on('change', function () { list(); }).append($('<option>').val('').text('Seleccione...'));
    data['categories'].forEach(product => {
        $selectProduct.append(
            $('<option>').val(product.id).text(product.name)
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
                        $('<th>').addClass('p-2').text('Expiración'),
                        $('<th>').addClass('p-2').text('Paquetes a Utilizar')
                    )
                ),
                $('<tbody>').attr('id', 'dataMaterial')
            )
        )
    );
    $('#bodygroup').empty().append($body);
}
function buildCardGenerated(data) {
    const $selectCategory = $('<select>').addClass('form-select').attr({ id: 'categoryId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectProduct = $('<select>').addClass('form-select').attr({ id: 'productId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectSubproduct = $('<select>').addClass('form-select').attr({ id: 'subproductId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectPresentation = $('<select>').addClass('form-select').attr({ id: 'presentationId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectLocation = $('<select>').addClass('form-select').attr({ id: 'locationId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectcondition = $('<select>').addClass('form-select').attr({ id: 'product_condition', required: true }).append($('<option>').val('').text('Seleccione...'));

    data['categories'].forEach(category => {
        $selectCategory.append(
            $('<option>').val(category.id).text(category.name)
        );
    });
    data['presentations'].forEach(presentation => {
        $selectPresentation.append(
            $('<option>').val(presentation.id).text(presentation.name)
                .attr({
                    'data-reference': presentation.reference_weight_lb
                })
        );
    });
    data['locations'].forEach(location => {
        $selectLocation.append(
            $('<option>').val(location.id).text(location.name)
        );
    });
    $selectcondition.append(
        $('<option>').val('fresco').text('Fresco'),
        $('<option>').val('congelado').text('Congelado'),
        $('<option>').val('descongelado').text('Descongelado'),
        $('<option>').val('dañado').text('Dañado'),
    );
    const $body = $('<div>').addClass('was-validated').append(
        $('<div>').addClass('row mb-3').append(
            $('<div>').addClass('col-lg-4 col-12').append(
                $('<label>')
                    .addClass('form-label fw-bold')
                    .append(
                        $('<span>').text('Categoría '),
                        $('<span>').addClass('text-danger').text('*')
                    ),
                $('<div>').addClass('input-group view-category').append(
                    $selectCategory,
                    $('<button>').addClass('btn btn-primary d-flex align-items-center')
                        .click(function () {
                            $('.view-category').css('display', 'none');
                            $('.view-addcategory').removeClass('d-none');
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('add_circle')
                        )
                ),
                $('<div>').addClass('input-group d-none view-addcategory').append(
                    $('<input>').addClass('form-control').attr({
                        type: 'text',
                        id: 'textCategory',
                        placeholder: 'Ingrese el nombre de la categoría',
                        required: true
                    }),
                    $('<button>').addClass('btn btn-danger d-flex align-items-center')
                        .click(function () {
                            $('#textCategory').val('');
                            $('.view-category').css('display', '');
                            $('.view-addcategory').addClass('d-none');
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('cancel')
                        ),
                    $('<button>').addClass('btn btn-success d-flex align-items-center')
                        .click(function () {
                            addCategory(null);
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('check_circle')
                        )
                )
            ),
            $('<div>').addClass('col-lg-4 col-12').append(
                $('<label>')
                    .addClass('form-label fw-bold')
                    .append(
                        $('<span>').text('Producto '),
                        $('<span>').addClass('text-danger').text('*')
                    ),
                $('<div>').addClass('input-group view-product').append(
                    $selectProduct,
                    $('<button>').addClass('btn btn-primary d-flex align-items-center disabled')
                        .click(function () {
                            $('.view-product').css('display', 'none');
                            $('.view-addproduct').removeClass('d-none');
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('add_circle')
                        )
                ),
                $('<div>').addClass('input-group d-none view-addproduct').append(
                    $('<input>').addClass('form-control').attr({
                        type: 'text',
                        id: 'textProduct',
                        placeholder: 'Ingrese el nombre del producto',
                        required: true
                    }),
                    $('<button>').addClass('btn btn-danger d-flex align-items-center')
                        .click(function () {
                            $('#textProduct').val('');
                            $('.view-product').css('display', '');
                            $('.view-addproduct').addClass('d-none');
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('cancel')
                        ),
                    $('<button>').addClass('btn btn-success d-flex align-items-center')
                        .click(function () {
                            addProduct(null);
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('check_circle')
                        )
                )
            ),
            $('<div>').addClass('col-lg-4 col-12').append(
                $('<label>')
                    .addClass('form-label fw-bold')
                    .append(
                        $('<span>').text('Subproducto '),
                        $('<span>').addClass('text-danger').text('*')
                    ),
                $('<div>').addClass('input-group view-subproduct').append(
                    $selectSubproduct,
                    $('<button>').addClass('btn btn-primary d-flex align-items-center disabled')
                        .click(function () {
                            $('.view-subproduct').css('display', 'none');
                            $('.view-addsubproduct').removeClass('d-none');
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('add_circle')
                        )
                ),
                $('<div>').addClass('input-group d-none view-addsubproduct').append(
                    $('<input>').addClass('form-control').attr({
                        type: 'text',
                        id: 'textSubproduct',
                        placeholder: 'Ingrese el nombre del subproducto',
                        required: true
                    }),
                    $('<button>').addClass('btn btn-danger d-flex align-items-center')
                        .click(function () {
                            $('#textSubproduct').val('');
                            $('.view-subproduct').css('display', '');
                            $('.view-addsubproduct').addClass('d-none');
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('cancel')
                        ),
                    $('<button>').addClass('btn btn-success d-flex align-items-center')
                        .click(function () {
                            addSubProduct(null);
                        })
                        .append(
                            $('<span>').addClass('material-symbols-outlined').text('check_circle')
                        )
                )
            )
        ),
        $('<div>').addClass('card border shadow mb-3').append(
            $('<div>').addClass('card-header bg-light').append(
                $('<div>').addClass('d-flex align-items-center text-primary').append(
                    $('<span>').addClass('material-symbols-outlined').text('info'),
                    $('<span>').addClass('fw-semibold ms-2').text('Información del Inventario')
                )
            ),
            $('<div>').addClass('card-body p-2').append(
                $('<div>').addClass('row').append(
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Presentación'),
                        $selectPresentation
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Cantidad de Paquetes'),
                        $('<input>').addClass('form-control decimal-only').attr({
                            type: 'text',
                            id: 'package_count',
                            placeholder: 'Cantidad de paquetes',
                            required: true
                        })
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Peso por paquete'),
                        $('<div>').addClass('input-group').append(
                            $('<input>').addClass('form-control decimal-only').attr({
                                type: 'text',
                                id: 'package_weight_lb',
                                placeholder: 'Peso por paquete',
                                required: true
                            }),
                            $('<span>').addClass('input-group-text rounded-0').text('LB')
                        ),
                        $('<div>').addClass('text-end text-success fw-semibold mx-1 reference').text('')
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Unidades por paquete'),
                        $('<input>').addClass('form-control numer-only').attr({
                            type: 'text',
                            id: 'units_per_package',
                            placeholder: 'Unidades por paquete'
                        })
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Ubicación'),
                        $selectLocation
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Peso Total'),
                        $('<input>').addClass('form-control').attr({
                            type: 'text',
                            id: 'quantity_lb',
                            placeholder: '0 Libras',
                            'disabled': true
                        })
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-3').append(
                        $('<label>').addClass('form-label fw-bold').text('Temperatura (°C)'),
                        $('<input>').addClass('form-control temperature entry_temperature').attr({
                            id: 'entry_temperature',
                            type: 'text',
                            placeholder: 'Ingrese la temperatura',
                            required: true
                        })
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-3').append(
                        $('<label>').addClass('form-label fw-bold').text('Condición del Producto'),
                        $selectcondition
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-3').append(
                        $('<label>').addClass('form-label fw-bold').text('Capacidad de la Materia Prima'),
                        $('<input>').addClass('form-control').attr({
                            type: 'text',
                            value: availableQuantity + ' Libras',
                            'disabled': true
                        })
                    ),
                )
            ),
        ),
        $('<div>').addClass('card border shadow mb-3').append(
            $('<div>').addClass('card-header bg-light').append(
                $('<div>').addClass('d-flex align-items-center text-primary').append(
                    $('<span>').addClass('material-symbols-outlined').text('package_2'),
                    $('<span>').addClass('fw-semibold ms-2').text('Información del Lote')
                )
            ),
            $('<div>').addClass('card-body p-2').append(
                $('<div>').addClass('row').append(
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Lote'),
                        $('<input>').addClass('form-control').attr({
                            id: 'batch_code',
                            type: 'text',
                            placeholder: 'Se generara uno automáticamente en caso de dejar en blanco'
                        })
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Fecha de Producción'),
                        $('<input>').addClass('form-control').attr({
                            id: 'production_date',
                            type: 'date',
                            required: true
                        })
                    ),
                    $('<div>').addClass('col-lg-4 col-12 mb-2').append(
                        $('<label>').addClass('form-label fw-bold').text('Fecha de Caducidad'),
                        $('<input>').addClass('form-control').attr({
                            id: 'expiration_date',
                            type: 'date',
                            required: true
                        })
                    ),
                    $('<div>').addClass('col-4 mb-3').append(
                        $('<label>').addClass('form-label fw-bold').text('Notas'),
                        $('<input>').addClass('form-control').attr({
                            id: 'notesbatch',
                            type: 'text',
                            placeholder: '¿Alguna observación?'
                        })
                    ),
                )
            ),
        ),
    );
    $('#bodygroup').empty().append($body);
}
function modalGeneric(id, action, title, subtitle, icon, size) {
    const $modal = buildModalGeneric(id, action, title, subtitle, icon);
    $('#modal').empty().append($modal);
    $('#' + id + ' .modal-dialog').addClass(size);
    $('#' + id + ' .modal-body').attr('id', 'bodygroup');
    $('#' + id).modal('show');
}
$(document).on('input', '#package_count, #package_weight_lb', function () {
    const package_count = parseFloat($('#package_count').val());
    const package_weight_lb = parseFloat($('#package_weight_lb').val());
    if (!isNaN(package_count) && !isNaN(package_weight_lb)) {
        const resultado = (package_count * package_weight_lb).toFixed(2);
        $('#quantity_lb').val(resultado + ' Libras');
    } else {
        $('#quantity_lb').val('');
    }
});
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

function onCategoryChangeG() {
    $('#categoryId').on('change', function () {
        const categoryId = parseInt($(this).val());
        if (!categoryId) {
            $('.view-product button').addClass('disabled');
            $('.view-subproduct button').addClass('disabled');
        }
        const category = catalogData.find(
            c => c.id === categoryId
        );
        $('#productId')
            .empty()
            .append('<option value="">Seleccione...</option>');
        $('#subproductId')
            .empty()
            .append('<option value="">Seleccione...</option>');
        if (!category) return;
        category.products.forEach(product => {
            $('#productId').append(
                $('<option>')
                    .val(product.id)
                    .text(product.name)
            );
        });
        $('.view-product button').removeClass('disabled');
    });
}
function onProductChangeG() {
    $('.view-subproduct button').addClass('disabled');
    $('#productId').on('change', function () {
        const categoryId = parseInt($('#categoryId').val());
        const productId = parseInt($(this).val());
        if (!categoryId) {
            $('.view-product button').addClass('disabled');
            $('.view-subproduct button').addClass('disabled');
        }
        if (!productId) {
            $('.view-subproduct button').addClass('disabled');
        }
        const category = catalogData.find(
            c => c.id === categoryId
        );
        if (!category) return;
        const product = category.products.find(
            p => p.id === parseInt($(this).val())
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
                    buildListMaterial(data.data);
                } else {
                    Notify(2, data.message);
                }
            }
        });
    }
}
function buildListMaterial(data) {
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
                <td>${item.expiration_date}</td>
                <td>
                    <input class="form-control form-control-sm decimal-only valmateria" placeholder="Cantidad...">
                </td>
            </tr>
        `;
        });
    } else {
        tbody = '<tr><td colspan="7" class="text-center">No hay stock del producto seleccionado</td></tr>';
    }
    $('#dataMaterial').empty().html(tbody);
}
function manageMaterial() {
    const product = $("#productId option:selected").text();
    const subproduct = $("#subproductId option:selected").text();
    const materiales = [];
    let hayError = false;
    $('#dataMaterial input.valmateria').each(function () {
        let valor = $(this).val().trim();
        if (valor !== '') {
            valor = Number(valor);
            const $tr = $(this).closest('tr');
            const idPackage = $tr.data('id');
            const batchId = $tr.data('batch');
            const maxPackage = $tr.data('packages');
            if (valor <= 0 || valor > maxPackage) {
                Notify(2, 'La cantidad de paquetes ingresada es incorrecta.');
                hayError = true;
                return false;
            }
            const weight = $tr.find('td:eq(3)').text().replace(/\D/g, '');
            const newQuantity = valor * Number(weight);
            materiales.push({
                id: idPackage,
                product: product,
                subproduct: subproduct,
                batchId: batchId,
                batch_code: $tr.find('td:eq(0)').text().trim(),
                ubicacion: $tr.find('td:eq(1)').text().trim(),
                presentacion: $tr.find('td:eq(2)').text().replace(/[0-9]/g, '').trim(),
                package_weight: $tr.find('td:eq(3)').text().trim(),
                quantity: newQuantity,
                expiration_date: $tr.find('td:eq(5)').text().trim(),
                inputPackage: valor
            });
        }
    });
    if (hayError) {
        return;
    }
    materiales.forEach(material => {
        const index = productionInputs.findIndex(item => item.id === material.id);
        if (index !== -1) {
            productionInputs[index] = material;
        } else {
            productionInputs.push(material);
        }
    });
    $('#modalMaterial').modal('hide');
    $('#modal').empty();
    buildMaterialPrima();
    if (materiales.length > 0) {
        Notify(1, 'Materia Prima agregada.');
    }
}
function manageProduct() {
    const package_count = Number($('#package_count').val());
    const package_weight_lb = Number($('#package_weight_lb').val());
    const quantity_lb = (package_count * package_weight_lb).toFixed(2);
    if (quantity_lb > availableQuantity) {
        Notify(2, 'La cantidad ingresada supera a la capacidad de la materia prima');
        return;
    }
    const data = {
        id: crypto.randomUUID(),
        categoryId: $('#categoryId').val(),
        productId: $('#productId').val(),
        productName: $("#productId option:selected").text(),
        subproductId: $('#subproductId').val(),
        subproductName: $("#subproductId option:selected").text(),
        presentationId: $('#presentationId').val(),
        presentationName: $("#presentationId option:selected").text(),
        package_count: package_count,
        package_weight_lb: package_weight_lb,
        units_per_package: $('#units_per_package').val(),
        locationId: $('#locationId').val(),
        locationName: $("#locationId option:selected").text(),
        quantity_lb: Number(quantity_lb),
        entry_temperature: $('#entry_temperature').val(),
        product_condition: $('#product_condition').val(),
        batch_code: $('#batch_code').val(),
        production_date: $('#production_date').val(),
        expiration_date: $('#expiration_date').val(),
        notesbatch: $('#notesbatch').val()
    };
    const required = [
        { key: 'categoryId', label: 'Categoría' },
        { key: 'productId', label: 'Producto' },
        { key: 'subproductId', label: 'Subproducto' },
        { key: 'presentationId', label: 'Presentación' },
        { key: 'package_count', label: 'Cantidad de paquetes' },
        { key: 'package_weight_lb', label: 'Peso por paquete' },
        { key: 'locationId', label: 'Ubicación' },
        { key: 'entry_temperature', label: 'Temperatura' },
        { key: 'product_condition', label: 'Condición del producto' },
        { key: 'production_date', label: 'Fecha de producción' },
        { key: 'expiration_date', label: 'Fecha de caducidad' }
    ];
    const missing = required.find(field => {
        const value = data[field.key];
        return value === null || value === undefined || String(value).trim() === '';
    });

    if (missing) {
        Notify(2, `Debe completar el campo: ${missing.label}`);
        return;
    }
    productionOutputs.push(data);
    availableQuantity = availableQuantity - quantity_lb;
    $('#modalGenerated').modal('hide');
    $('#modal').empty();
    buildProductGenerated();
}
function buildMaterialPrima() {
    let html = '';
    let cont = 1;
    let resumeQuantity = 0;
    productionInputs.forEach(item => {
        resumeQuantity += Number(item.quantity);
        html += `
            <tr data-id="${item.id}">
                <td class="text-center">${cont}</td>
                <td>${item.product}</td>
                <td>${item.subproduct}</td>
                <td>${item.ubicacion}</td>
                <td>${item.batch_code}</td>
                <td>${item.inputPackage + ' ' + item.presentacion + ' de ' + item.package_weight}</td>
                <td>${item.quantity} Libras</td>
            </tr>
        `;
        cont++;
    });
    $('#tbodyMaterialPrima').empty().html(html);
    $('#resumenItemsMaterial').text(`${cont - 1} Items`);
    $('#resumenTotalMaterial').text(`${resumeQuantity} Libras`);
    maxAvailableQuantity = resumeQuantity;
    if (productionOutputs.length > 0) {
        let valueOutput = 0;
        productionOutputs.forEach(item => {
            valueOutput += Number(item.quantity_lb);
        });
        availableQuantity = maxAvailableQuantity - valueOutput;
    } else {
        availableQuantity = resumeQuantity;
    }

    resumeProduction();
}
function buildProductGenerated() {
    let html = '';
    let cont = 1;
    let resumeQuantity = 0;
    productionOutputs.forEach(item => {
        resumeQuantity += Number(item.quantity_lb);
        html += `
            <tr data-id="${item.id}">
                <td class="text-center">${cont}</td>
                <td>${item.productName}</td>
                <td>${item.subproductName}</td>
                <td>${item.locationName}</td>
                <td>${Number(item.package_count) + ' ' + item.presentationName}</td>
                <td>${Number(item.package_weight_lb)} Libras</td>
                <td>${Number(item.quantity_lb)} Libras</td>
                <td class="text-center">
                    <span class="material-symbols-outlined edit text-danger h4 btn-delete-product">
                        delete
                    </span>
                </td>
            </tr>
        `;
        cont++;
    });
    $('#tbodyProductGenerated').empty().html(html);
    $('#resumenItemsGenerated').text(`${cont - 1} Items`);
    $('#resumenTotalGenerated').text(`${resumeQuantity} Libras`);
    resumeProduction();
}
function resumeProduction() {
    $('.indicador1').text(`${maxAvailableQuantity} Libras`);
    $('.indicador2').text(`${(maxAvailableQuantity - availableQuantity)} Libras`);
    $('.indicador3').text(`${availableQuantity} Libras`);
    $('.indicador4').text(`${((availableQuantity / maxAvailableQuantity) * 100).toFixed(2)}%`);
    $('.indicador5').text(`${productionInputs.length} Items`);
    $('.indicador6').text(`${productionOutputs.length} Items`);
}
/*$('#tbodyMaterialPrima').on('click', '.btn-delete-material', function () {
    const id = $(this).closest('tr').data('id');
    productionInputs = productionInputs.filter(item => item.id !== id);
    buildMaterialPrima();
    resumeProduction();
});*/
$('#tbodyProductGenerated').on('click', '.btn-delete-product', function () {
    const id = $(this).closest('tr').data('id');
    const product = productionOutputs.find(item => item.id === id);
    if (product) {
        availableQuantity += Number(product.quantity_lb);
    }
    productionOutputs = productionOutputs.filter(item => item.id !== id);
    buildProductGenerated();
    resumeProduction();
});
function addCategory(id) {
    if ($('#textCategory').val().trim() === '') {
        Notify(2, 'El nombre de la categoría es obligatorio');
        return;
    }
    const data = {
        name: $('#textCategory').val()
    };
    sendAjax({
        endpoint: '/Categorias/create',
        type: 'POST',
        data: data,
        beforeSend: function () {
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                catalogData.push({
                    ...datos,
                    products: []
                });
                $('#categoryId').append(
                    $('<option>')
                        .val(datos.id)
                        .text(datos.name)
                        .prop('selected', true)
                );
                $('#categoryId').trigger('change');
                Notify(1, data.message);
                $('#textCategory').val('');
                $('.view-category').css('display', '');
                $('.view-addcategory').addClass('d-none');
            } else {
                Notify(2, data.message);
            }
        }
    });
}
function addProduct(id) {
    if (
        $('#categoryId').val().trim() === '' ||
        $('#textProduct').val().trim() === '') {
        Notify(2, 'El nombre del producto es obligatorio');
        return;
    }
    const data = {
        name: $('#textProduct').val(),
        categoryId: $('#categoryId').val()
    };
    sendAjax({
        endpoint: '/Productos/create',
        type: 'POST',
        data: data,
        beforeSend: function () {
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                const categoria = catalogData.find(
                    c => c.id == datos.category_id
                );

                if (categoria) {
                    categoria.products.push({
                        ...datos,
                        subproducts: []
                    });
                }
                $('#productId').append(
                    $('<option>')
                        .val(datos.id)
                        .text(datos.name)
                        .prop('selected', true)
                );
                $('#productId').trigger('change');
                Notify(1, data.message);
                $('#textProduct').val('');
                $('.view-product').css('display', '');
                $('.view-addproduct').addClass('d-none');
            } else {
                Notify(2, data.message);
            }
        }
    });
}
function addSubProduct(id) {
    if (
        $('#categoryId').val().trim() === '' ||
        $('#productId').val().trim() === '' ||
        $('#textSubproduct').val().trim() === ''
    ) {
        Notify(2, 'El nombre del subproducto es obligatorio');
        return;
    }
    const data = {
        name: $('#textSubproduct').val(),
        productId: $('#productId').val()
    };
    sendAjax({
        endpoint: '/Subproductos/create',
        type: 'POST',
        data: data,
        beforeSend: function () {
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                const producto = catalogData
                    .flatMap(categoria => categoria.products)
                    .find(producto => producto.id == datos.product_id);

                if (producto) {
                    producto.subproducts.push(datos);
                }
                $('#subproductId').append(
                    $('<option>')
                        .val(datos.id)
                        .text(datos.name)
                        .prop('selected', true)
                );
                $('#subproductId').trigger('change');
                Notify(1, data.message);
                $('#textSubproduct').val('');
                $('.view-subproduct').css('display', '');
                $('.view-addsubproduct').addClass('d-none');
            } else {
                Notify(2, data.message);
            }
        }
    });
}
function create() {
    if (productionOutputs.length <= 0) {
        Notify(2, 'No hay productos generados');
        return;
    }
    const totalInput = productionInputs.reduce((sum, item) => sum + Number(item.quantity), 0);
    const totalLoss = Number(availableQuantity);

    let assignedLoss = 0;

    const input = productionInputs.map((item, index) => {
        const quantity = Number(item.quantity);
        let loss;
        if (index === productionInputs.length - 1) {
            loss = Number((totalLoss - assignedLoss).toFixed(2));
        } else {
            loss = Number((((quantity / totalInput) * totalLoss)).toFixed(2));
            assignedLoss += loss;
        }
        return {
            id: item.id,
            batchId: item.batchId,
            inputPackage: item.inputPackage,
            quantity,
            loss
        };
    });
    const data = {
        resume: {
            total: totalInput,
            merma: totalLoss
        },
        input,
        output: productionOutputs.map(item => ({
            categoryId: Number(item.categoryId),
            productId: Number(item.productId),
            subproductId: Number(item.subproductId),
            presentationId: Number(item.presentationId),
            locationId: Number(item.locationId),
            package_count: item.package_count,
            package_weight_lb: item.package_weight_lb,
            units_per_package: item.units_per_package,
            quantity_lb: item.quantity_lb,
            entry_temperature: item.entry_temperature,
            product_condition: item.product_condition,
            batch_code: item.batch_code,
            production_date: item.production_date,
            expiration_date: item.expiration_date,
            notesbatch: item.notesbatch
        }))
    };

    sendAjax({
        endpoint: '/Produccion/create',
        type: 'POST',
        data: data,
        beforeSend: function () {
            $('.btn-create ').prop('disabled', true).empty().append(
                $('<span>').addClass('spinner-border spinner-border-sm').attr({ role: 'status', 'aria-hidden': 'true' }).text(''),
                $('<span>').addClass('ms-2').text('Guardando...')
            );
        },
        onSuccess: (data) => {
            if (data.success) {
                location.reload();
            } else {
                $('.btn-create').prop('disabled', false).empty().append(
                    $('<span>').addClass('material-symbols-outlined').text('check'),
                    $('<span>').addClass('ms-2').text('Confirmar Producción')
                );
                Notify(2, data.message);
            }
        }
    });
}
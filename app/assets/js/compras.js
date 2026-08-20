let catalogData = [];
let productData = [];
$(document).ready(function () {
    //activar tabs del menu
    $('#compras').addClass('selected');
    $('#compras a').addClass('active');

    //proceso compras
    bindButtonClick('.btn-createProduct', createProduct);
    bindButtonClick('.btn-addProductList', addProductList);
    bindButtonClick('.btn-createPurchase', createPurchase);

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
        endpoint: '/Compras/catalogs',
        beforeSend: function () {
            cardProduct();
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                catalogData = datos['categories'];
                buildCardProducto(datos);
                onCategoryChange();
                onProductChange();
                onPresentationChange();
            } else {
                Notify(2, data.message);
            }
        }
    });
}
function cardProduct() {
    const $modal = $('<div>').addClass('modal fade').attr({
        id: 'modalProducto',
        tabindex: '-1',
        'aria-labelledby': 'modalProductoLabel',
        'aria-hidden': 'true',
        'data-bs-backdrop': 'static',
        'data-bs-keyboard': 'false'
    });
    const $dialog = $('<div>').addClass('modal-dialog modal-lg');
    const $content = $('<div>').addClass('modal-content');
    const $header = $('<div>').addClass('modal-header')
        .append(
            $('<h1>')
                .addClass('modal-title fs-5')
                .attr('id', 'modalProductoLabel')
                .text('Agregar Producto')
        )
        .append(
            $('<button>')
                .addClass('btn-close')
                .attr({
                    type: 'button',
                    'data-bs-dismiss': 'modal',
                    'aria-label': 'Close'
                })
        );
    const $body = $('<div>').addClass('modal-body').attr({ id: 'modalBodyProduct' });
    const $footer = $('<div>').addClass('modal-footer')
        .append(
            $('<button>')
                .addClass('btn btn-primary btn-addProductList')
                .attr({
                    type: 'button'
                })
                .text('Guardar')
        );
    $content.append($header, $body, $footer);
    $dialog.append($content);
    $modal.append($dialog);

    $('body').append($modal);

    $('#oculto').empty().append($modal);
    $('#modalProducto').modal('show');
}
function buildCardProducto(data) {
    const $selectCategory = $('<select>').addClass('form-select').attr({ id: 'categoryId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectProduct = $('<select>').addClass('form-select').attr({ id: 'productId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectSubproduct = $('<select>').addClass('form-select').attr({ id: 'subproductId', required: true }).append($('<option>').val('').text('Seleccione...'));
    const $selectPresentation = $('<select>').addClass('form-select rounded-0').attr({ id: 'presentationId', required: true }).append($('<option>').val('').text('Seleccione...'));
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
    const $inputQuantity = $('<div>').addClass('input-group').append(
        $('<input>').addClass('form-control decimal-only rounded-0').attr({
            type: 'text',
            id: 'unitVal',
            placeholder: 'Peso adquirido',
            required: true
        }),
        $('<span>').addClass('input-group-text rounded-0').text('LB')
    );
    const $inputCost = $('<div>').addClass('money-wrapper')
        .append(
            $('<span>').addClass('money-symbol').text('$'),
            $('<input>').addClass('form-control rounded-0 money-input decimal-only')
                .attr({
                    type: 'text',
                    id: 'priceVal',
                    placeholder: 'Costo Unitario',
                    required: true
                })
                .on('keyup', function (e) {
                    calculateTotal('price');
                })
        );
    const $inputTotal = $('<div>').addClass('money-wrapper')
        .append(
            $('<span>').addClass('money-symbol').text('$'),
            $('<input>').addClass('form-control rounded-0 money-input decimal-only')
                .attr({
                    type: 'text',
                    id: 'totalVal',
                    placeholder: 'Costo Total',
                    required: true
                })
                .on('keyup', function (e) {
                    calculateTotal('total');
                })
        );
    const $body = $('<div>').addClass('was-validated').append(
        $('<div>').addClass('mb-2').append(
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
        $('<div>').addClass('mb-2').append(
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
        $('<div>').addClass('mb-2').append(
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
        ),
        $('<div>').addClass('row g-2 mb-2').append(
            $('<div>').addClass('col-12 fw-bold')
                .append(
                    $('<span>').text('Peso y costo adquirido '),
                    $('<span>').addClass('text-danger').text('*')
                ),
            $('<div>').addClass('col-12 col-md-6 p-0').append($inputQuantity, $('<div>').addClass('text-end text-success fw-semibold mx-1 referenceVal').text('')),
            $('<div>').addClass('col-6 col-md-3 p-0').append($inputCost),
            $('<div>').addClass('col-6 col-md-3 p-0').append($inputTotal)
        ),
        $('<div>').addClass('row g-2 mb-2').append(
            $('<div>').addClass('col-12 fw-bold')
                .append(
                    $('<span>').text('Tipo de Paquete '),
                    $('<span>').addClass('text-danger').text('*')
                ),
            $('<div>').addClass('col-6 col-md-6 p-0').append($selectPresentation),
            $('<div>').addClass('col-6 col-md-6 p-0').append(
                $('<input>').addClass('form-control decimal-only rounded-0').attr({
                    type: 'text',
                    id: 'presentationVal',
                    placeholder: 'Cantidad de paquetes',
                    required: true
                })
            ),
            $('<div>').addClass('col-6 col-md-6 p-0').append(
                $('<input>').addClass('form-control numer-only rounded-0').attr({
                    type: 'text',
                    id: 'unidadesVal',
                    placeholder: 'Unidades por paquete'
                })
            ),
            $('<div>').addClass('col-6 col-md-6 p-0').append(
                $('<div>').addClass('input-group').append(
                    $('<input>').addClass('form-control decimal-only rounded-0').attr({
                        type: 'text',
                        id: 'referenceVal',
                        placeholder: 'Peso por paquete'
                    }),
                    $('<span>').addClass('input-group-text rounded-0').text('LB')
                ),
                $('<div>').addClass('text-end text-success fw-semibold mx-1 reference').text('')
            )
        ),
        $('<div>').addClass('mb-2').append(
            $('<label>').addClass('form-label fw-bold').text('Fecha de Caducidad'),
            $('<input>').addClass('form-control').attr({
                id: 'expiration_date',
                type: 'date'
            })
        ),
        $('<div>').addClass('mb-2').append(
            $('<label>').addClass('form-label fw-bold').text('Notas'),
            $('<textarea>').addClass('form-control').attr({
                id: 'notesdetails',
                type: 'text'
            })
        )
    );
    $('#modalBodyProduct').empty().append($body);
}
function addProductList(id) {
    if ($('#categoryId').val() === ''
        || $('#productId').val() === ''
        || $('#subproductId').val() === ''
        || $('#unitVal').val() === ''
        || $('#priceVal').val() === ''
        || $('#totalVal').val() === ''
        || $('#presentationId').val() === ''
        || $('#presentationVal').val() === ''
    ) {
        Notify(2, 'Por favor complete todos los campos obligatorios');
        return;
    }
    const data = {
        categoryId: $('#categoryId').val(),
        categoryName: $('#categoryId option:selected').text(),
        productId: $('#productId').val(),
        productName: $('#productId option:selected').text(),
        subproductId: $('#subproductId').val(),
        subproductName: $('#subproductId option:selected').text(),
        quantity: $('#unitVal').val(),
        price: $('#priceVal').val(),
        total: $('#totalVal').val(),
        presentationId: $('#presentationId').val(),
        presentationName: $('#presentationId option:selected').text(),
        presentationQuantity: $('#presentationVal').val(),
        presentationReference: $('#referenceVal').val(),
        unidadesVal: $('#unidadesVal').val(),
        expiration_date: $('#expiration_date').val(),
        notes: $('#notesdetails').val()
    };
    productData.push(data);
    $('#modalProducto').modal('hide');
    $('#oculto').empty();
    buildTableProducts();
}
function buildTableProducts() {
    const $tbody = $('#productsContent');
    if (productData.length === 0) {
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
    productData.forEach(product => {
        totalPurchase += parseFloat(product.total);
        quantityTotal += parseFloat(product.quantity);
        quantityProduct++;
    });
    $tbody.empty();
    let cont = 1;
    productData.forEach((product, index) => {
        $tbody.append(
            $('<tr>').attr('data-index', index).append(
                $('<td>').text(cont),
                $('<td>').text(product.productName),
                $('<td>').text(product.subproductName),
                $('<td>').text(`${product.presentationQuantity} ${product.presentationName}`),
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
function createPurchase(id) {
    const requiredFields = [
        '#supplier',
        '#invoice_number',
        '#purchase_date',
        '#payment_method',
        '#status'
    ];

    if (!validateRequiredFields(requiredFields)) {
        Notify(2, 'Complete los campos de datos de compra');
        return;
    }
    if (!productData.length > 0) {
        Notify(2, 'Debe agregar productos a la compra.');
        return;
    }
    const data = {
        supplierId: $('#supplier').val(),
        invoice_number: $('#invoice_number').val(),
        purchase_date: $('#purchase_date').val(),
        subtotal: $('#subtotal').val(),
        taxes: $('#taxes').val(),
        total: $('#total').val(),
        payment_method: $('#payment_method').val(),
        status: $('#status').val(),
        notes: $('#notes').val(),
        products: productData
    }
    sendAjax({
        endpoint: '/Compras/create',
        type: 'POST',
        data: data,
        beforeSend: function () {
            $('#btncreatePurchase').prop('disabled', true).empty().append(
                $('<span>').addClass('spinner-border spinner-border-sm').attr({ role: 'status', 'aria-hidden': 'true' }).text(''),
                $('<span>').addClass('ms-2').text('Guardando...')
            );
        },
        onSuccess: (data) => {
            if (data.success) {
                Notify(1, data.message);
                setTimeout(() => {
                    window.location.href = '../Compras';
                }, 1500);
            } else {
                $('#btncreatePurchase').prop('disabled', false).empty().append(
                    $('<span>').addClass('material-symbols-outlined').text('check_circle'),
                    $('<span>').addClass('ms-2').text('Registrar Compra')
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
    $('#oculto').empty().append(modal);
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
function onCategoryChange() {
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
function onProductChange() {
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
function onPresentationChange() {
    $('#presentationId').on('change', function () {
        const reference = $(this)
            .find(':selected')
            .data('reference');
        $('#referenceVal').val(reference || '');
    });

}
function calculateTotal(source) {
    const quantity = parseFloat($('#unitVal').val());
    if (isNaN(quantity) || quantity <= 0) return;
    if (source === 'price') {
        const price = parseFloat($('#priceVal').val());
        if (!isNaN(price)) {
            $('#totalVal').val((quantity * price).toFixed(2));
        }
    }
    if (source === 'total') {
        const total = parseFloat($('#totalVal').val());
        if (!isNaN(total)) {
            $('#priceVal').val((total / quantity).toFixed(2));
        }
    }
}
$(document).on('click', '.delete', function () {
    const index = $(this).closest('tr').data('index');
    productData.splice(index, 1);
    buildTableProducts();
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
$(document).on('input', '#presentationVal', function () {
    const package_count = parseFloat($('#presentationVal').val());
    const quantity = parseFloat($('#unitVal').val());
    if (
        !isNaN(package_count) &&
        !isNaN(quantity) &&
        package_count > 0
    ) {
        const valor = (quantity / package_count).toFixed(2);
        $('.reference').text('¿Tal vez es ' + valor + '?');
    } else {
        $('.reference').text('');
    }
});
$(document).on('input', '#presentationVal, #referenceVal', function () {
    const presentationVal = parseFloat($('#presentationVal').val());
    const referenceVal = parseFloat($('#referenceVal').val());
    if (!isNaN(presentationVal) && !isNaN(referenceVal)) {
        const resultado = presentationVal * referenceVal;
        $('.referenceVal').text('¿Tal vez es ' + resultado + '?');
    }
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
        endpoint: "/Compras/update",
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


$(document).ready(function () {
    $('#stock').addClass('selected');
    $('#stock a').addClass('active');
    bindButtonClick('.btn-merma', modalDecrease);
    bindButtonClick('.btn-confirm-merma', confirmDecrease);
});

function modalDecrease() {
    const selectedPackages = [];

    document.querySelectorAll('.package-checkbox:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        selectedPackages.push({
            id: row.dataset.id,
            location: row.dataset.location,
            producto: row.cells[0].innerText.trim(),
            cantidadEmpaques: row.cells[1].innerText.trim(),
            pesoEmpaque: row.cells[2].innerText.trim(),
            libras: row.cells[3].innerText.trim(),
            lote: row.cells[4].innerText.trim(),
            fechaProduccion: row.cells[5].innerText.trim(),
            fechaExpiracion: row.cells[6].innerText.trim()
        });
    });

    if (!selectedPackages.length > 0) {
        return;
    }

    const html = `
    <div class="modal fade" id="modalMerma" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalMermaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-semibold" id="modalMermaLabel">Registrar Merma</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                ${selectedPackages.map(item => `
                <div class="card border mb-2 shadow-sm">
                    <div class="card-header bg-light p-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined">collections_bookmark</span>
                            <strong>${item.lote}</strong>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start mb-3 d-none">
                            <div>
                                <div class="fw-bold text-uppercase">${item.producto}</div>
                                <div class="small text-muted">
                                    ${item.cantidadEmpaques} · ${item.pesoEmpaque} · ${item.libras}
                                    <span class="badge text-bg-light ms-1">${item.lote}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-danger fw-semibold">Exp: ${item.fechaExpiracion}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center text-center mb-2">
                            <div class="flex-fill">
                                <span class="material-symbols-outlined text-primary">package</span>
                                <div class="small text-dark">Paquetes</div>
                                <strong>${item.cantidadEmpaques}</strong>
                            </div>
                            <div class="flex-fill">
                                <span class="material-symbols-outlined text-primary">inventory</span>
                                <div class="small text-dark">Peso del Paquete</div>
                                <strong>${item.pesoEmpaque}</strong>
                            </div>
                            <div class="flex-fill">
                                <span class="material-symbols-outlined text-primary">inventory</span>
                                <div class="small text-dark">Peso Total</div>
                                <strong>${item.libras}</strong>
                            </div>
                            <div class="flex-fill">
                                <span class="material-symbols-outlined text-primary">snowflake</span>
                                <div class="small text-dark">Ubicación</div>
                                <strong>${item.location}</strong>
                            </div>
                            <div class="flex-fill">
                                <span class="material-symbols-outlined text-primary">event_busy</span>
                                <div class="small text-dark">Expiración</div>
                                <strong>${item.fechaExpiracion}</strong>
                            </div>
                        </div>
                        <div class="row decrease" data-id="${item.id}" data-weight-package="${item.pesoEmpaque.replace(/\D/g, '')}" data-max-packages="${item.cantidadEmpaques.replace(/\D/g, '')}">
                            <div class="col-lg-6 col-12">
                                <div class="border rounded p-2">
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Paquetes Perdidos (Perdida Total)</label>
                                        <input type="text" placeholder="Cantidad de Paquetes perdidos" class="form-control form-control-sm numer-only package-count-lost">
                                    </div>
                                    <div>
                                        <label class="form-label small fw-semibold">Motivo de la Merma</label>
                                        <input type="text" class="form-control form-control-sm notes"  placeholder="Ingrese un motivo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="border rounded p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="form-label fw-semibold small">Empaques con nuevo peso (Perdida Parcial)</div>
                                        <button type="button" class="btn btn-outline-primary btn-sm add-weight-row" data-id="${item.id}" data-presentation="${item.cantidadEmpaques.replace(/[0-9]/g, '').trim()}">
                                            <span class="material-symbols-outlined align-middle fs-6">add</span>
                                            Agregar
                                        </button>
                                    </div>

                                    <div id="weights-${item.id}">
                                        <div class="input-group weight-row mb-2">
                                            <span class="input-group-text"> 1 ${item.cantidadEmpaques.replace(/[0-9]/g, '').trim()}</span>
                                            <input type="text" class="form-control form-control-sm new-weight rounded-0 decimal-only quantity" placeholder="Nuevo peso (lb)">
                                            <input type="text" class="form-control form-control-sm new-weight rounded-0 numer-only units-per-package" placeholder="Unidades">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-0 remove-weight-row">
                                                <span class="material-symbols-outlined align-middle fs-6">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                `).join('')}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-confirm-merma">Confirmar Merma</button>
                </div>
            </div>
        </div>
    </div>
    `;
    $('#modal').empty().html(html);
    $('#modalMerma').modal('show');
}
$(document).on('click', '.add-weight-row', function () {
    const id = $(this).data('id');
    const presentation = $(this).data('presentation');
    $(`#weights-${id}`).append(`
        <div class="input-group weight-row mb-2">
            <span class="input-group-text"> 1 ${presentation}</span>
            <input type="text" class="form-control form-control-sm new-weight rounded-0 decimal-only quantity" placeholder="Nuevo peso (lb)">
            <input type="text" class="form-control form-control-sm new-weight rounded-0 numer-only units-per-package" placeholder="Unidades">
            <button type="button" class="btn btn-outline-danger btn-sm rounded-0 remove-weight-row">
                <span class="material-symbols-outlined align-middle fs-6">delete</span>
            </button>
        </div>
    `);
});
$(document).on('click', '.remove-weight-row', function () {
    $(this).closest('.weight-row').remove();
});

document.querySelectorAll('.location-group').forEach(group => {

    new Sortable(group, {
        group: 'packages',
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'table-warning',
        draggable: 'tr[data-id]',
        onStart: function (evt) {
            evt.item.originalParent = evt.from;
            evt.item.originalNextSibling = evt.item.nextElementSibling;
        },
        onEnd: function (evt) {
            if (evt.from.dataset.locationId === evt.to.dataset.locationId) {
                return;
            }
            modalTraslate({
                evt,
                packageId: evt.item.dataset.id,
                oldLocationId: evt.from.dataset.locationId,
                newLocationId: evt.to.dataset.locationId,
                expiratedDate: evt.item.dataset.expirationDate,
                maxPackages: parseInt(evt.item.dataset.packageCount),
                completed: false
            });
        }
    });
});

function restoreRow(evt) {
    const row = evt.item;
    if (row.originalNextSibling) {
        row.originalParent.insertBefore(
            row,
            row.originalNextSibling
        );
    } else {
        row.originalParent.appendChild(row);
    }
}
function modalTraslate(move) {
    currentMove = move;
    const [dia, mes, anio] = move.expiratedDate.split('/');
    const html = `
    <div class="modal fade" id="modalMove" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-semibold">Mover Inventario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="fw-bold">Cantidad de paquetes</label>
                        <input id="moveQuantity" class="form-control numer-only" min="1" max="${move.maxPackages}" placeholder="Máximo ${move.maxPackages}">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Condición</label>
                        <select id="moveCondition" class="form-select">
                            <option value="congelado">Congelado</option>
                            <option value="fresco">Fresco</option>
                            <option value="descongelado">Descongelado</option>
                            <option value="dañado">Dañado</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Temperatura</label>
                        <input id="moveTemperature" class="form-control temperature entry_temperature" placeholder="Temperatura">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Fecha de Expiración</label>
                        <input type="date" id="moveExpirated" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button id="btnConfirmMove" type="button" class="btn btn-primary">Confirmar</button>
                </div>

            </div>
        </div>
    </div>`;
    $("#modal").html(html);
    $('#moveExpirated').val(`${anio}-${mes}-${dia}`);
    const modal = new bootstrap.Modal(document.getElementById("modalMove"));
    modal.show();
    $("#modalMove").on("hidden.bs.modal", function () {
        if (!currentMove.completed) {
            restoreRow(currentMove.evt);
        }
        currentMove = null;
    });
    $("#btnConfirmMove").on("click", confirmMove);
}
function confirmMove() {
    const quantity = parseInt($("#moveQuantity").val());
    if (!quantity || quantity < 1) {
        Notify(2, "Ingrese una cantidad válida.");
        return;
    }
    if (quantity > currentMove.maxPackages) {
        Notify(2, `No puede mover más de ${currentMove.maxPackages} paquetes.`);
        return;
    }
    const data = {
        packageId: currentMove.packageId,
        ubicacionId: currentMove.newLocationId,
        oldLocationId: currentMove.oldLocationId,
        quantityToMove: quantity,
        condition: $("#moveCondition").val(),
        temperature: $("#moveTemperature").val(),
        expiration_date: $("#moveExpirated").val()
    };
    sendAjax({
        endpoint: "/Stock/move",
        type: "POST",
        data,
        onSuccess: (data) => {
            if (data.success) {
                currentMove.completed = true;
                location.reload();
            } else {
                $("#modalMove").modal("hide");
                Notify(2, data.message);
            }
        }
    });
}
function confirmDecrease() {
    try {
        const data = decreaseData();
        if (data != null) {
            sendAjax({
                endpoint: '/Stock/decrease',
                type: 'POST',
                data,
                onSuccess: (data) => {
                    if (data.success) {
                        location.reload();
                        console.log(data);
                    } else {
                        Notify(2, data.message);
                    }
                }
            });
        } else {
            Notify(2, 'Rellene el formulario');
        }

    } catch (error) {
        Notify(2, error);
    }

}
function decreaseData() {
    const decreases = [];
    document.querySelectorAll('.decrease').forEach(item => {
        const weightPackage = Number(item.dataset.weightPackage);
        const maxPackages = Number(item.dataset.maxPackages);
        const packageCountLost = Number(item.querySelector('.package-count-lost')?.value || 0);
        const weights = [];
        let totalWeightPackages = 0;
        item.querySelectorAll('.weight-row').forEach(row => {
            const weight = {
                quantity: row.querySelector('.quantity')?.value.trim() ?? '',
                units_per_package: row.querySelector('.units-per-package')?.value.trim() ?? ''
            };
            const values = Object.values(weight);
            if (values.some(v => v !== '')) {
                if (Number(weight.quantity) <= 0) {
                    throw new Error('El peso debe ser mayor a 0.');
                }
                if (Number(weight.quantity) >= weightPackage) {
                    throw new Error('El nuevo peso debe ser menor al peso actual');
                }
                weights.push(weight);
                totalWeightPackages += Number(1);
            }
        });
        const totalPackages = packageCountLost + totalWeightPackages;
        if (totalPackages > maxPackages) {
            throw new Error(`La cantidad de paquetes no puede superar ${maxPackages}.`);
        }
        decreases.push({
            id: item.dataset.id,
            package_count: packageCountLost,
            notes: item.querySelector('.notes')?.value.trim() ?? '',
            weights
        });
    });
    const hasData = decreases.some(item =>
        item.package_count > 0 || item.weights.length > 0
    );
    if (!hasData) {
        return null;
    }
    return decreases;
}
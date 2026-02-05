/**
 * Helpers compartidos para Alpine.js
 * Elimina duplicacion de formatGs, item management, y confirmaciones
 */

// ============================================
// FORMATEO DE MONEDA (Guaranies)
// ============================================
window.formatGs = function(value) {
    return new Intl.NumberFormat('es-PY').format(value) + ' Gs.';
};

// ============================================
// TOAST HELPER (SweetAlert2)
// Uso: toast('Guardado correctamente') o toast('Error', 'error')
// ============================================
window.toast = function(message, icon = 'success', timer = 3000) {
    if (typeof Swal === 'undefined') return;
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
    });
};

// ============================================
// FORMULARIO DE ITEMS CON PRECIOS
// Usado en: pedidos-cliente/create, pedidos-cliente/edit, ordenes-compra/create
// ============================================
window.itemFormWithPrices = function(config = {}) {
    const quantityField = config.quantityField || 'cantidad';
    const defaultItem = {};
    defaultItem.producto_id = '';
    defaultItem[quantityField] = 1;
    defaultItem.precio_unitario = 0;
    defaultItem.subtotal = 0;

    return {
        items: config.items || [{ ...defaultItem }],
        descuento: config.descuento || 0,
        descuentoMonto: 0,
        subtotal: config.subtotal || 0,
        total: config.total || 0,
        _quantityField: quantityField,

        init() {
            if (config.items) {
                this.items.forEach((_, index) => this.calcularSubtotal(index));
            }
        },

        addItem() {
            this.items.push({ ...defaultItem });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calcularTotal();
        },

        updatePrecio(index, event) {
            const select = event ? event.target : document.querySelector(`select[name="items[${index}][producto_id]"]`);
            const option = select.options[select.selectedIndex];
            if (option && option.dataset.precio) {
                this.items[index].precio_unitario = parseInt(option.dataset.precio) || 0;
                this.calcularSubtotal(index);
            }
        },

        calcularSubtotal(index) {
            const qty = parseFloat(this.items[index][this._quantityField]) || 0;
            const price = parseInt(this.items[index].precio_unitario) || 0;
            this.items[index].subtotal = Math.round(qty * price);
            this.calcularTotal();
        },

        calcularTotal() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.subtotal || 0), 0);
            const pct = Math.min(100, Math.max(0, parseFloat(this.descuento) || 0));
            this.descuentoMonto = Math.round(this.subtotal * pct / 100);
            this.total = Math.max(0, this.subtotal - this.descuentoMonto);
        },

        formatGs: window.formatGs
    };
};

// ============================================
// FORMULARIO DE ITEMS SIN PRECIOS
// Usado en: solicitudes-presupuesto/create
// ============================================
window.itemFormSimple = function(config = {}) {
    return {
        items: config.items || [{ producto_id: '', cantidad: 1 }],

        addItem() {
            this.items.push({ producto_id: '', cantidad: 1 });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        }
    };
};

// ============================================
// CONFIRMACION CON SWEETALERT2
// Uso: <form @submit.prevent="confirmSubmit($event, { title: '...', text: '...' })">
// ============================================
window.confirmSubmit = function(event, options = {}) {
    const form = event.target;
    const defaults = {
        title: '\u00bfEst\u00e1s seguro?',
        text: 'Esta acci\u00f3n no se puede deshacer.',
        icon: 'warning',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'S\u00ed, confirmar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    };

    const config = { ...defaults, ...options, showCancelButton: true };

    if (typeof Swal !== 'undefined') {
        Swal.fire(config).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm(config.text)) {
            form.submit();
        }
    }
};

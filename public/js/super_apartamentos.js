/**
 * @file super_apartamentos.js
 * @description Lógica interactiva para el módulo de gestión inmobiliaria (root).
 * - Validación frontend y feedback visual de guardado.
 * - Envíos nativos compatibles con firewalls y servidores compartidos (InfinityFree).
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Envío de formulario para Registrar Condominio ──────────────────────
    const formCondominio = document.querySelector('#Mcondominiotorre form');
    const btnCondo = document.getElementById('btn-guardar-condo') || formCondominio?.querySelector('button[type="submit"]');

    if (formCondominio) {
        formCondominio.addEventListener('submit', function (e) {
            const nombre = document.getElementById('condo-name')?.value?.trim();
            const metros = parseFloat(document.getElementById('condo-metros')?.value);

            if (!nombre) {
                e.preventDefault();
                alert('El nombre del condominio es obligatorio.');
                return;
            }

            if (isNaN(metros) || metros <= 0) {
                e.preventDefault();
                alert('El total de metros cuadrados debe ser un número mayor a 0.');
                return;
            }

            // Feedback visual y bloqueo anti doble clic
            if (btnCondo) {
                btnCondo.disabled = true;
                btnCondo.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Guardando Condominio...';
            }
        });
    }

    // ── Envío de formulario para Registrar Apartamentos ───────────────────
    const formApartamento = document.querySelector('#Mbloqueapartamento form');
    const btnApto = document.getElementById('btn-guardar-apto') || formApartamento?.querySelector('button[type="submit"]');

    if (formApartamento) {
        formApartamento.addEventListener('submit', function (e) {
            const condoId     = document.getElementById('apto-condo')?.value;
            const numInicial  = parseInt(document.getElementById('apto-num-inicial')?.value, 10);
            const cantidad    = parseInt(document.getElementById('apto-cantidad')?.value, 10);
            const metrosApto  = parseFloat(document.getElementById('apto-metros')?.value);

            if (!condoId) {
                e.preventDefault();
                alert('Debe seleccionar un condominio o torre.');
                return;
            }

            if (isNaN(numInicial) || numInicial <= 0) {
                e.preventDefault();
                alert('El número inicial debe ser mayor a 0.');
                return;
            }

            if (isNaN(cantidad) || cantidad <= 0) {
                e.preventDefault();
                alert('La cantidad a generar debe ser mayor a 0.');
                return;
            }

            if (isNaN(metrosApto) || metrosApto <= 0) {
                e.preventDefault();
                alert('Los metros cuadrados por apartamento deben ser mayor a 0.');
                return;
            }

            // Feedback visual y bloqueo anti doble clic
            if (btnApto) {
                btnApto.disabled = true;
                btnApto.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Generando Apartamentos...';
            }
        });
    }
});

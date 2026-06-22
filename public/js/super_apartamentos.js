/**
 * @file super_apartamentos.js
 * @description Lógica interactiva para el módulo de gestión inmobiliaria (root).
 * - Rellena automáticamente el select de condominio_id en el modal de apartamentos
 *   cuando el usuario abre el modal, usando los valores del modal de condominios.
 */
document.addEventListener('DOMContentLoaded', function () {

    // Confirmación antes de registrar un condominio
    const formCondominio = document.querySelector('#Mcondominiotorre form');
    if (formCondominio) {
        formCondominio.addEventListener('submit', function (e) {
            const nombre = document.getElementById('condo-name')?.value?.trim();
            if (!nombre) {
                e.preventDefault();
                alert('El nombre del condominio es obligatorio.');
            }
        });
    }

    // Confirmación antes de registrar un apartamento
    const formApartamento = document.querySelector('#Mbloqueapartamento form');
    if (formApartamento) {
        formApartamento.addEventListener('submit', function (e) {
            const num = document.getElementById('apto-num')?.value?.trim();
            if (!num) {
                e.preventDefault();
                alert('El número de apartamento es obligatorio.');
            }
        });
    }
});

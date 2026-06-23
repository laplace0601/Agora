/**
 * @file super_apartamentos.js
 * @description Lógica interactiva para el módulo de gestión inmobiliaria (root).
 * - Rellena automáticamente el select de condominio_id en el modal de apartamentos
 *   cuando el usuario abre el modal, usando los valores del modal de condominios.
 */
document.addEventListener('DOMContentLoaded', function () {

    // AJAX para registrar un condominio
    const formCondominio = document.querySelector('#Mcondominiotorre form');
    if (formCondominio) {
        formCondominio.addEventListener('submit', async function (e) {
            e.preventDefault(); // Evitar envío nativo

            const nombre = document.getElementById('condo-name')?.value?.trim();
            if (!nombre) {
                alert('El nombre del condominio es obligatorio.');
                return;
            }

            const formData = new FormData(formCondominio);

            try {
                const response = await fetch(formCondominio.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                // Siempre renovamos el token CSRF si viene en la respuesta
                if (result.csrf) {
                    const csrfInput = formCondominio.querySelector('input[name="csrf_test_name"]');
                    if (csrfInput) {
                        csrfInput.value = result.csrf;
                    }
                }

                if (response.ok && result.status === 'success') {
                    // Recargar la página para ver el nuevo condominio
                    window.location.reload();
                } else {
                    alert(result.error || 'Error al registrar el condominio.');
                }
            } catch (error) {
                console.error('Error en la petición:', error);
                alert('Ocurrió un error de conexión.');
            }
        });
    }

    // ── AJAX para registrar apartamentos masivamente ───────────────────────────
    const formApartamento = document.querySelector('#Mbloqueapartamento form');
    if (formApartamento) {
        formApartamento.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Validaciones frontend rápidas
            const numInicial = parseInt(document.getElementById('apto-num-inicial')?.value);
            const cantidad   = parseInt(document.getElementById('apto-cantidad')?.value);

            if (!numInicial || numInicial <= 0) {
                Swal.fire({ icon: 'error', title: 'Campo requerido', text: 'El número inicial debe ser mayor a 0.' });
                return;
            }
            if (!cantidad || cantidad <= 0) {
                Swal.fire({ icon: 'error', title: 'Campo requerido', text: 'La cantidad a generar debe ser mayor a 0.' });
                return;
            }

            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                text: `Generando ${cantidad} apartamento(s). Por favor espera.`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const formData = new FormData(formApartamento);

            try {
                const response = await fetch(formApartamento.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();

                // Renovar token CSRF si viene en la respuesta
                if (result.csrf) {
                    const csrfInput = formApartamento.querySelector('input[type="hidden"]');
                    if (csrfInput) csrfInput.value = result.csrf;
                }

                if (response.ok && result.status === 'success') {
                    // Toast de éxito + recarga de página
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).fire({ icon: 'success', title: result.message });

                    setTimeout(() => window.location.reload(), 1500);

                } else if (result.error_type === 'plan_limit') {
                    // ── Advertencia especial para límite de plan ───────────────
                    Swal.fire({
                        icon: 'warning',
                        title: '⚠️ Límite del plan alcanzado',
                        html: `<p class="mb-0">${result.error}</p>`,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#f59e0b',
                        background: '#fffbeb',
                        customClass: {
                            title: 'text-warning',
                            htmlContainer: 'text-start'
                        }
                    });

                } else {
                    // Error genérico
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.error || 'No se pudieron registrar los apartamentos.'
                    });
                }

            } catch (err) {
                console.error('Error de red:', err);
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
            }
        });
    }
});

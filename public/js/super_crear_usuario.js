/**
 * @file super_crear_usuario.js
 * @description Mantiene activa la pestaña seleccionada tras recargas y añade confirmaciones de seguridad.
 */
document.addEventListener("DOMContentLoaded", function () {
    // 1. Guardar la pestaña activa en el almacenamiento del navegador (localStorage)
    const activeTabKey = 'agora_super_active_tab';
    const tabElements = document.querySelectorAll('#roleTabs button');
    
    tabElements.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            localStorage.setItem(activeTabKey, e.target.id);
        });
    });

    // 2. Leer la última pestaña usada al recargar la página
    const lastActiveTabId = localStorage.getItem(activeTabKey);
    if (lastActiveTabId) {
        const tabToActivate = document.getElementById(lastActiveTabId);
        if (tabToActivate) {
            const tabTrigger = new bootstrap.Tab(tabToActivate);
            tabTrigger.show();
        }
    }

    // 3. Confirmación visual antes de crear un Súper Usuario Maestro (Seguridad extra)
    const superForm = document.querySelector('#panel-super form');
    if (superForm) {
        superForm.addEventListener('submit', function (e) {
            const confirmar = confirm("🚨 ATENCIÓN: Estás a punto de crear un Súper Usuario con acceso total al núcleo del sistema. ¿Deseas continuar?");
            if (!confirmar) {
                e.preventDefault(); // Cancela el envío si el usuario se arrepiente
            }
        });
    }
});
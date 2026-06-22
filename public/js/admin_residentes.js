/**
 * @file admin_residentes.js
 * @description Buscador en tiempo real ya incluido en la vista HTML.
 * Este archivo existe como placeholder para futuras funcionalidades
 * (ej: exportar directorio, filtrar por estado de cuenta, etc.)
 */
document.addEventListener('DOMContentLoaded', function () {
    // El buscador ya está implementado directamente en la vista admin/residentes.php
    // Aquí se pueden añadir filtros adicionales en el futuro.
    
    // Animación de entrada para las filas de la tabla
    const filas = document.querySelectorAll('#tablaResidentes tbody tr');
    filas.forEach((fila, i) => {
        fila.style.opacity = '0';
        fila.style.transform = 'translateY(8px)';
        fila.style.transition = `opacity 0.25s ease ${i * 40}ms, transform 0.25s ease ${i * 40}ms`;
        requestAnimationFrame(() => {
            fila.style.opacity = '1';
            fila.style.transform = 'translateY(0)';
        });
    });
});

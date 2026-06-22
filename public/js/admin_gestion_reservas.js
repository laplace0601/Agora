function confirmarAccionReserva(url, accion) {
    const mensaje = accion === 'aprobar' 
        ? "📅 ¿Estás seguro de que deseas APROBAR esta reserva? Se bloqueará la fecha."
        : "🚨 ¿Estás seguro de que deseas RECHAZAR esta reserva? El día quedará liberado.";
    if (confirm(mensaje)) { window.location.href = url; }
}
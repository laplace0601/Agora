
    <!-- Footer del panel admin -->
    <footer class="border-top mt-5 py-3 bg-white">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <span class="text-muted small">&copy; <?= date('Y') ?> Sistema Ágora — Gestión de Condominios.</span>
            <span class="text-muted small">Panel Administrativo</span>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle (Popper incluido) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS específico de la página (ej: admin_residentes.js) -->
    <?php if (isset($pagina_actual)): ?>
        <script src="<?= base_url('js/' . $pagina_actual . '.js?v=' . time()) ?>"></script>
    <?php endif; ?>

</body>
</html>

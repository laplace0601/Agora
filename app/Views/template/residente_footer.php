<footer class="container text-center py-4 mt-5 border-top text-muted small">
        &copy; 2026 Sistema Ágora — Gestión Residencial Premium.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($pagina_actual)): ?>
        <script src="<?= base_url('js/' . $pagina_actual . '.js') ?>"></script>
    <?php endif; ?>
</body>
</html>
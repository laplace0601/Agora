<?php
/**
 * @file super_footer.php
 * @description Cierre de etiquetas y carga dinámica de lógica JavaScript corporativa.
 */
?>
</div> <footer class="container text-center py-4 mt-5 border-top text-muted small">
    &copy; 2026 Ágora Platform Inc. — Consola Maestra de Infraestructura Global.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($pagina_actual) && !empty($pagina_actual)): ?>
    <script src="<?= base_url('js/' . $pagina_actual . '.js') ?>"></script>
<?php endif; ?>

</body>
</html>
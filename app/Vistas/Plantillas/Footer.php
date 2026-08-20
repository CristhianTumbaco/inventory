</div>
</div>
</div>
<script src="<?php echo RUTA_WEB; ?>/assets/js/sweetalert2.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/js/notify.min.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/libs/jquery/dist/jquery.min.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/libs/select2/select2.min.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/js/sidebarmenu.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/js/app.min.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/libs/simplebar/dist/simplebar.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/libs/datatables/datatables.min.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/libs/datatables/jquery.highlight.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/libs/datatables/dataTables.searchHighlight.min.js"></script>
<script src="<?php echo RUTA_WEB; ?>/assets/js/xlsx.full.min.js"></script>
<?php if (!empty($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
        <?php
        $src = str_starts_with($script, '/')
            ? RUTA_WEB . $script
            : RUTA_WEB . '/assets/js/' . $script;
        ?>
        <script src="<?= $src ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<?php
if (isset($_SESSION['ok'])) {
    echo "<script>Notify(1,'" . $_SESSION['ok'] . "')</script>";
    unset($_SESSION['ok']);
}
if (isset($_SESSION['error'])) {
    echo "<script>Notify(2,'" . $_SESSION['error'] . "')</script>";
    unset($_SESSION['error']);
}
?>
</body>

</html>
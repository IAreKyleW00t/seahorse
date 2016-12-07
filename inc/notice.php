<?php
    require_once('inc/session.php');
    if (isset($_SESSION['NOTICE'])) : ?>
<!-- Notice -->
<script>
    $(document).ready(function() {
        var $content = $('<span><?php echo $_SESSION['NOTICE']; ?></span>');
        Materialize.toast($content, 5000);
    });
</script>
<?php endif;
    unset($_SESSION['NOTICE']); // Remove old message
?>

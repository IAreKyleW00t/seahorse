<?php
    /* This file will automatically remove any expired tokens from the
        global `tokens` table. These tokens then become usable again,
        although it if very unlikely they will be. */
    $sql = include('inc/sql_connection.php');

    $query = $sql->prepare('DELETE FROM tokens WHERE (expires_on - NOW()) <= 0');
    $query->execute();
?>

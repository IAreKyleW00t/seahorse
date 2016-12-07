<?php
    /* This file will automatically create a new PDO connection based on the
        values saved within the projects configuration. This PDO object is
        improved by enforcing real prepared statements in MySQL and automatically
        closing the connetion once completed. */
    require_once('inc/config.php');
    
    $pdo = new PDO(SQL_TYPE . ':host=' . SQL_HOST . ';dbname=' . SQL_DB . ';charset=utf8', SQL_USER, SQL_PASSWD); // Open connection
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Softfail on errors
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Real prepared statements
    $pdo->setAttribute(PDO::ATTR_PERSISTENT, false); // Disable persistent connection
    return $pdo;
?>
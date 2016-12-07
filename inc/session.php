<?php
    /* This file will automatically start a PHP session if a session has not
        already been started. ("session_start()" was never called.) 
        
       For improved security, we also harden the session cookie by setting an
        expiration time (30 minutes), enforcing HTTPS on the cookie if the host
        supports it, and randomly regenerating the session ID. */
    
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(1800, '/', $_SERVER['SERVER_NAME'], (isset($_SERVER['HTTPS']) ? true : false), true); // Set session cookie
        session_start(); // Start session
    }
    if (mt_rand(0, 4) === 0) session_regenerate_id(true); // Regenerate session ID
?>
<?php
    require_once('inc/session.php');

    /* Completely clear out the SESSION variable. */
    $_SESSION = array();

    /* Completely remove all SESSION-related cookies.*/
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    /* Destroy the SESSION and all data associated with it. */
    session_destroy();

    /* Redirect the user to index page once completed. */
    header('Location: /');
    exit;
?>
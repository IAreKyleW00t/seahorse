<?php
    /* This file will create a function, sendmail, that will send an email to
        a specified recipient with the given subject, message, and sender. All
        headers are set to meed RFC qualifications.
        
       See: http://php.net/manual/en/function.mail.php */
    
    function sendmail($to, $subject, $message, $from, $name) {
        /* Create a unique email token and message ID. */
        $token = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
        $message_id = "<" . uniqid() . ".$token@" . $_SERVER['SERVER_NAME'] . ">";
        
        /* Set up email headers so they meet RFC qualifications. */
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=utf8";
        $headers[] = "Message-id: $message_id";
        $headers[] = "From: '$name' <$from>";
        $headers[] = "Reply-To: '$name' <$from>";
        $headers[] = "Date: " . date(DATE_RFC2822);
        $headers[] = "Return-Path: <$from>";
        $headers[] = "X-Priority: 3";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        /* Send email to recipient. */
        mail($to, $subject, $message, implode("\r\n", $headers), "-f $from");
    }
?>
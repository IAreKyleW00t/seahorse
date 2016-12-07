<?php
    /* This file will create a function, reCAPTCHA, that will automatically
        validate a reCAPTCHA form based on the given input. This is done by
        sending the data to Google's servers and parsing the response.
        
       This function requires a RECAPTCHA_SECRET to be provided within thep
       projects configuration. */
    require_once('inc/config.php');
    
    function reCAPTCHA($g) {
        $url = 'https://www.google.com/recaptcha/api/siteverify'; // Google server
        $data = 'secret=' . RECAPTCHA_SECRET . '&response=' . $g . '&remoteip=' . $_SERVER['REMOTE_ADDR']; // Data
        
        /* Create curl request. */
        $req = curl_init($url);
        curl_setopt($req, CURLOPT_POST, 1); // POST
        curl_setopt($req, CURLOPT_POSTFIELDS, $data); // Add POST
        curl_setopt($req, CURLOPT_FOLLOWLOCATION, 1); // Follow to end
        curl_setopt($req, CURLOPT_HEADER, 0); // No Header
        curl_setopt($req, CURLOPT_RETURNTRANSFER, 1); // Return after done
        $response = curl_exec($req); // Exec request
        
        /* Parse and return response. */
        $json = json_decode($response, true); // Decode from JSON
        return bool($json['success']);
    }
?>
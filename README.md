# **Cookie Session Handler**
 
Handler class that stores session data in cookies
 

## Basics
 
For web application with high traffic we use different modes to saving session data; example: files and databases.
 
To manage session of web application with high traffic it becomes difficult with files or a database as a mode to save session data.
 
This class provides solution to this problem by using COOKIE as a storage media in a safer way with encryption.
 
To use the encryption we need to follow few steps to create the base64 encoded $key and $iv as below.
 

    $key = openssl_random_pseudo_bytes(32); // 256-bit key
    $iv = openssl_random_pseudo_bytes(16); // 128-bit IV
    
    /*
    * Store the below base64 encoded key and IV somewhere safe in the code
    */
    
    $key_base64 = base64_encode($key); // <BASE64_KEY>
    $iv_base64 = base64_encode($vi);   // <BASE64_IV>

## Storing above base64 encoded key and iv in config file
 

    define('BASE64_KEY', '<BASE64_KEY>');
    define('BASE64_IV', '<BASE64_IV>');

## Instructions to use
 
Include the below file in scripts to use session
 

    include_once ('CookieSessionHandler.php');
    session_start();

> Note: The amount of details that can be stored in session depends on the length of string cookie supports in a browser.
<?php

/**
 * Return the password encrypted
 * 
 * @param $string
 * @return string
 */
function security_encrypt($string) {

    $string =  SEED1 . $string . SEED2;
    $string = md5($string);
    return $string;

}

/**
 * Check if the value is a number
 *
 * @param $string
 * @return bool
 */
function check_integer($string) {

    return (is_numeric($string) && $string == intval($string));
    
}

/**
 * Sanitize string before db insertions 
 *
 * @param $string
 * @return string
 */
function sanitize_string($string, $conn = null) {

    if($conn != null) {
        $conn->real_escape_string($string);
    }

    $string = htmlentities($string, ENT_QUOTES, "utf-8");
    $string = stripslashes($string);

    return $string;
}
?>
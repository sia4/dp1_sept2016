<?php
include_once 'sanitize.php';
session_start();

/**
 * Session Utilities
 */
function logged(){
    return isset($_SESSION['username']);
}

function set_session($username){

    $_SESSION['username'] = $username;

}

function get_username(){
    if(!logged()){
        return null;
    }
    return $_SESSION['username'];
}

function log_out(){

    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        
        $params = session_get_cookie_params();
        
        setcookie(session_name(), '', time() - 3600*24, 
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        
    }
    
    session_destroy();
}

function check_inactivity_time(){

    $t = time();
    $diff = 0;
    
    if (isset($_SESSION['time'])){
        $t0 = $_SESSION['time'];
        $diff = ($t - $t0);  // inactivity period
    } 
    
    if ($diff > 120) { // new or with inactivity period too long
        log_out();
    } else {
        $_SESSION['time'] = time();
    }
}

/**
 *  Generate the html code for books table
 */
function generate_books_table() {

    echo '<table class="data">
        <tbody>
        <tr class="header">
            <th>Quantity to buy</th>
            <th>Price for buying</th>
            <th>Price for selling</th>
            <th>Quantity to sell</th>
        </tr>';
        $buy = get_buy_books();
        $sell = get_sell_books();
        for($i  = 0; $i < max(count($buy), count($sell)); $i++){
            echo '<tr>';
            if(isset($buy[$i])) {
                echo '<td>'.$buy[$i]["quantity"].'</td>
                <td>'.$buy[$i]["price"].'</td>';
            } else {
                echo '<td></td>
                <td></td>';
            }
            if(isset($sell[$i])) {
                echo '<td>'.$sell[$i]["price"].'</td>
                <td>'.$sell[$i]["quantity"].'</td>';
            } else {
                echo '<td></td>
                <td></td>';
            }
            echo '</tr>';
        }
        while($i < 5) {
            echo '<tr><td></td><td> </td><td> </td><td> </td></tr>';
            $i++;
        }
    echo '</tbody></table>';

}
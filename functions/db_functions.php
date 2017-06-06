<?php

include_once '/../config.php';
include_once 'sanitize.php';

$table_users = "users";
$table_books = "bookings";
$table_history = "history";

/**
 * Open connection
 *
 * @return connection opened
 */
function create_connection(){

    global $host, $username, $password, $dbname;
    $conn = new mysqli($host,$username, $password, $dbname);
    if ($conn->connect_errno) {
        echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
        die;
    }

    $conn->set_charset("utf8");
    
    return $conn;

}

/**
 * Create tables
 *
 * @param $string
 * @return string
 *
 */
function init(){

    global $table_books;

    $conn = create_connection();

    $query =   "TRUNCATE $table_bookings;
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (2, 1000, 0);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (10, 960, 0);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (4, 950, 0);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (3, 900, 0);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (8, 800, 0);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (3, 1030, 1);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (11, 1050, 1);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (8, 1100, 1);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (6, 1150, 1);
                INSERT INTO $table_bookings(quantity, price, buy_sell_flag) VALUES (15, 1200, 1);";

    if($conn->query($query) === false){
        close_connection($conn);
        return false;
    }

    close_connection($conn);
    return true;
}

/**
 * Close connection
 *
 * @param connection
 */
function close_connection($conn){

    $conn->close();

}

/**
 * Insert a new user
 *
 * @param $username, $password
 * @return bool
 */
function create_user($username, $pw, $name, $surname) {

    global $table_users;

    if($username === "" || $pw === "" || $name === "" || $surname === ""){
        return false;
    }

    $conn = create_connection();

    $user = sanitize_string($username, $conn);
    $name = sanitize_string($name, $conn);
    $surname = sanitize_string($surname, $conn);

    if($user === false || $name === false || $surname === false){
        close_connection($conn);
        return false;
    }

    $pw = security_encrypt($pw);
    

    $query = "INSERT INTO $table_users(email,password,name,surname,actual_amount,n_stocks)
              VALUES ('$user','$pw', '$name', '$surname', 50000, 0)";

    if($conn->query($query) == false){
        close_connection($conn);
        return false;
    }

    close_connection($conn);
    return true;

}

/**
 * During log in check username and pw
 *
 * @param $username, $password
 * @return bool
 */
function check_username_password($username, $pw){

    global $table_users;

    if($username === "" || $pw === ""){
        return false;
    }
    
    $conn = create_connection();

    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $pw = security_encrypt($pw);

    $query = "SELECT COUNT(*) FROM $table_users WHERE email = '$user' AND password = '$pw'";
    $result = $conn->query($query);
    if($result == false){
        close_connection($conn);
        return false;
    }
    $count = $result->fetch_array(MYSQLI_NUM);
    $result->free();
    if($count[0] == 0){
        close_connection($conn);
        return false;
    }else {
        close_connection($conn);
        return true;
    }

}

/**
 * Get logged user info
 *
 * @param $username
 * @return array
 */
function get_user_info($username){

    global $table_users;

    if($username === ""){
        return false;
    }

    $conn = create_connection();

    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $query = "SELECT * FROM $table_users WHERE email = '$user'";
    $result = $conn->query($query);
    if($result == false){
        close_connection($conn);
        return false;
    }

    $row = $result->fetch_array(MYSQLI_ASSOC);

    return $row;

}

/**
 * Get logged user history
 *
 * @param $username
 * @return array
 */
function get_user_history($username){

    global $table_history;

    if($username === ""){
        return false;
    }

    $conn = create_connection();

    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $query = "SELECT * FROM $table_history WHERE email = '$user'";
    $result = $conn->query($query);
    if($result == false){
        close_connection($conn);
        return false;
    }

    $history = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $history[] = $row;
    }

    return $history;
}
/**
 * Get all the books referred to a buy operation
 * in decresing cost order
 *
 * @return array
 */
function get_buy_books() {
    global $table_books;

    $buy = array();

    $conn = create_connection();
    $query = "SELECT * FROM $table_books
              WHERE buy_sell_flag = 0 ORDER BY price DESC";
    $result = $conn->query($query);
    if($result == false){
        close_connection($conn);
        return false;
    }

    while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $buy[] = $row;
    }

    return $buy;
}


/**
 * Get all the books referred to a sell operation
 * in increasing cost order
 *
 * @return array
 */
function get_sell_books() {
    global $table_books;

    $sell = array();

    $conn = create_connection();
    $query = "SELECT * FROM $table_books
              WHERE buy_sell_flag = 1 ORDER BY price";
    $result = $conn->query($query);
    if($result == false){
        close_connection($conn);
        return false;
    }

    while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $sell[] = $row;
    }

    return $sell;
}

/**
 * Insert the operation in the database and update the total amount and
 * number of stock of that user
 *
 * @return if the operation has been performed
 */
function insert_operation($username, $operation, $quantity) {

    global $table_users, $table_books, $table_history;

    if($username === ""){
        return 0;
    }

    if(!check_integer($operation)) {
        return 0;
    }

    if($operation != 0 && $operation != 1) {
        return 0;
    }

    if(!check_integer($quantity)){
        return 0;
    }

    if($quantity < 1 ){
        return 0;
    }
    $conn = create_connection();

    $conn->autocommit(false);

    $query = "SELECT * FROM $table_users
              WHERE email = '$username' FOR UPDATE";
    $result = $conn->query($query);
    if($result == false){
        close_connection($conn);
        return 0;
    }

    $user = $result->fetch_array(MYSQLI_ASSOC);

    if($operation == 0) {

        $sum_quantity = 0;
        $sum_cost = 0;

        $query = "SELECT * FROM $table_books
              WHERE buy_sell_flag = 1 ORDER BY price
              FOR UPDATE";
        $result = $conn->query($query);
        if($result == false){
            close_connection($conn);
            return 0;
        }

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $buy[$row['id']] = $row;
        }

        $id_to_delete = array();
        $last_id = -1;
        foreach ($buy as $b) {
            if($sum_quantity + $b['quantity'] < $quantity) {
                $sum_quantity += $b['quantity'];
                $id_to_delete[] = $b['id'];
                $sum_cost += $b['quantity']*$b['price'];
            } else if($sum_quantity + $b['quantity'] == $quantity) {
                $sum_quantity += $b['quantity'];
                $id_to_delete[] = $b['id'];
                $sum_cost += $b['quantity']*$b['price'];
                break;
            } else if($sum_quantity + $b['quantity'] > $quantity) {
                $last_id = $b['id'];
                $last_id_quantity_new = $sum_quantity + $b['quantity'] - $quantity;
                $last_id_quantity_to_add = $b['quantity'] - $last_id_quantity_new;
                $sum_quantity = $quantity;
                $sum_cost += $last_id_quantity_to_add*$b['price'];
                break;
            }
        }
        if($sum_quantity < $quantity) {
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return -1;
        }

        if ($sum_cost > $user['actual_amount']) {
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return -2;
        }

        $time = date("Y-m-d H:m:s");
        foreach ($id_to_delete as $idtd) {

            $query = "DELETE from $table_books WHERE id = $idtd";
            $result = $conn->query($query);
            if($result === false){
                $conn->rollback();
                $conn->autocommit(true);
                close_connection($conn);
                return 0;
            }
        }

        if($last_id > -1) {

            $query = "UPDATE $table_books SET quantity = $last_id_quantity_new WHERE id = $last_id";
            $result = $conn->query($query);
            if($result === false){
                $conn->rollback();
                $conn->autocommit(true);
                close_connection($conn);
                return 0;
            }
        }

        $new_amount = $user['actual_amount'] - $sum_cost;
        $new_n_stocks = $user['n_stocks'] + $quantity;
        $query = "UPDATE $table_users SET actual_amount = $new_amount, n_stocks = $new_n_stocks
        WHERE email = '$username'";
        $result = $conn->query($query);
        if($result === false){
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return 0;
        }

        $query = "INSERT INTO $table_history(email, time, quantity, total_price, buy_sell_flag)
                      VALUES('$username', '$time', $quantity, $sum_cost, 0)";
        $result = $conn->query($query);
        if ($result === false) {
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return 0;
        }

        $conn->commit();
        $conn->autocommit(true);
        close_connection($conn);
        return 1;

    } else if($operation == 1) {

        if ($quantity > $user['n_stocks']) {
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return -3;
        }

        $sum_quantity = 0;
        $sum_gain = 0;

        $query = "SELECT * FROM $table_books
              WHERE buy_sell_flag = 0 ORDER BY price DESC
              FOR UPDATE";
        $result = $conn->query($query);
        if ($result == false) {
            close_connection($conn);
            return 0;
        }

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $sell[$row['id']] = $row;
        }

        $id_to_delete = array();
        $last_id = -1;
        foreach ($sell as $s) {
            if ($sum_quantity + $s['quantity'] < $quantity) {
                $sum_quantity += $s['quantity'];
                $id_to_delete[] = $s['id'];
                $sum_gain += $s['quantity'] * $s['price'];
            } else if ($sum_quantity + $s['quantity'] == $quantity) {
                $sum_quantity += $s['quantity'];
                $id_to_delete[] = $s['id'];
                $sum_gain += $s['quantity'] * $s['price'];
                break;
            } else if ($sum_quantity + $s['quantity'] > $quantity) {
                $last_id = $s['id'];
                $last_id_quantity_new = $sum_quantity + $s['quantity'] - $quantity;
                $last_id_quantity_to_sub = $s['quantity'] - $last_id_quantity_new;
                $sum_quantity = $quantity;
                $sum_gain += $last_id_quantity_to_sub * $s['price'];
                break;
            }
        }
        if ($sum_quantity < $quantity) {
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return -4;
        }

        $time = date("Y-m-d H:m:s");
        foreach ($id_to_delete as $idtd) {

            $query = "DELETE from $table_books WHERE id = $idtd";
            $result = $conn->query($query);
            if ($result === false) {
                $conn->rollback();
                $conn->autocommit(true);
                close_connection($conn);
                return 0;
            }
        }

        if ($last_id > -1) {

            $query = "UPDATE $table_books SET quantity = $last_id_quantity_new WHERE id = $last_id";
            $result = $conn->query($query);
            if ($result === false) {
                $conn->rollback();
                $conn->autocommit(true);
                close_connection($conn);
                return 0;
            }
        }

        $new_amount = $user['actual_amount'] + $sum_gain;
        $new_n_stocks = $user['n_stocks'] - $quantity;
        $query = "UPDATE $table_users SET actual_amount = $new_amount, n_stocks = $new_n_stocks
        WHERE email = '$username'";
        $result = $conn->query($query);
        if ($result === false) {
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return 0;
        }

        $query = "INSERT INTO $table_history(email, time, quantity, total_price, buy_sell_flag)
                      VALUES('$username', '$time', $quantity, $sum_gain, 1)";

        $result = $conn->query($query);
        if ($result === false) {
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return 0;
        }
        $conn->commit();
        $conn->autocommit(true);
        close_connection($conn);
        return 1;

    }
    $conn->rollback();
    $conn->autocommit(true);
    close_connection($conn);
    return 0;

}
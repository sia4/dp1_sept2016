<!DOCTYPE HTML>
<html lang="en">
<?php
include __DIR__.'/config.php';
require_once ('functions/functions.php');
require_once ('functions/db_functions.php');

/* HTTPS */
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

/* COOKIES */
$page = explode('/', $_SERVER['PHP_SELF']);
if(!isset($_SESSION['cookie_enabled']) && $page[count($page) - 1] != 'cookie.php') {
    $_SESSION['page'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    setcookie('test', 'test', time() + 3600);
    header("location: cookie.php");
    exit;
}

/* SESSION EXPIRED */
check_inactivity_time();

?>
<head>
    <title>Polishop</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="js/jquery-3.0.0.min.js"></script>
    <script type="text/javascript" src="js/jsfunctions.js"></script>
    <link href="stylesheet/style.css" rel="stylesheet" type="text/css">
    <noscript>JavaScript is disabled. In this way the website could not work well.</noscript>
</head>

<body>

    <div id="header">
        <div id="title_header"><p>Polishop</p></div>
    </div>
    <div id="menu">
        <div><h2>MENU</h2></div>

        <?php
            /* Highlight the current menu element */
            $page = explode('/', $_SERVER['PHP_SELF']);

                switch ($page[count($page) - 1]){
                    case 'index.php':
                        $active_el = 1;
                        break;
                    case 'login.php':
                        $active_el = 2;
                        break;
                    case 'logout.php':
                        $active_el = 3;
                        break;
                    case 'neworder.php':
                        $active_el = 4;
                        break;
                    case 'user.php':
                        $active_el = 5;
                        break;
                    default:
                        $active_el = 1;
                        break;
                }

        ?>

        <div><a class="menu_el <?php if($active_el == 1) echo 'active'; ?>" href="index.php">Home</a></div>
            <?php if (!logged()) { ?>
                
                <div><a class="menu_el <?php if($active_el == 2) echo 'active'; ?>" href="login.php">Login / Sign Up</a></div>
            <?php }else { ?>
                <div><a class="menu_el <?php if($active_el == 3) echo 'active'; ?>" href="neworder.php">New Order</a></div>
                <div><a class="menu_el <?php if($active_el == 3) echo 'active'; ?>" href="user.php">Personal Information</a></div>
                <div><a class="menu_el <?php if($active_el == 3) echo 'active'; ?>" href="logout.php">Log Out</a></div>
            <?php } ?>
    </div>
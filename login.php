<?php

include_once 'header.php';

if(isset($_POST["login"])) {

    $login = check_username_password($_POST['username_l'], $_POST['password_l']);
    if($login){
        set_session($_POST['username_l']);
        header("Location: index.php");
        exit;
    }
}
if(isset($_POST['register'])){
    $return = create_user($_POST['username_r'], $_POST['password_r'], $_POST['name_r'], $_POST['surname_r']);
}

if(!logged()){?>
    <div id="container">
        <div id="login_box">
            <h2>Log In</h2>
            <p class="message">Log in to start trading!</p>
            <form id="login_form" method="post" action="">
            <input type="email" placeholder="Email" id="username_l" name="username_l"/><br>
            <input type="password" placeholder="Password" id="password_l" name="password_l"/><br>
            <input type="submit" id="login" name="login" value="Log in"/>
            </form>
            <?php
            if(isset($login) && !$login){?>
               <p class="error">Wrong password or mail, please try again!</p>
        <?php
            }
            ?>
        </div>
        <div id="register_box">
            <h2>Aren't you registred yet? Sign up!</h2>
            <form id="register_form" method="post" action="">
                <input type="text" placeholder="Name" id="name_r" name="name_r"/><br>
                <input type="text" placeholder="Surname" id="surname_r" name="surname_r"/><br>
                <input type="email" placeholder="Email" id="username_r" name="username_r"/><br>
                <input type="password" placeholder="Password" id="password_r" name="password_r"/><br>
                <input type="password" placeholder="Check password" id="check_password_r"/><br>
                <input type="submit" name="register" id="register" value="Register"/>
            </form>
            <span id="form_error_r"></span>
            <?php
            if(isset($return) && $return){?>
                <p class="success">Successfully registered! Log in to book your seats.</p>
                <?php
            }elseif(isset($return) && !$return){?>
                <p class="error">Something went wrong during the operation, please try again!</p>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}
include('footer.php');
?>
<?php
require_once('header.php');

if(logged()) {
    if (isset($_POST["new_order"])) {
        $neworder = insert_operation(get_username(), $_POST['operation'], $_POST['quantity']);
    }
    ?>

    <div id="container">
        <h2>New Order</h2>
        <p class="message">Select the operation you want to perform and insert the quantity.</p>
        <form id="new_order_form" method="post" action="">
            <label for="operation">Operation:</label>
            <input type="radio" name="operation" value="0"/> Buy
            <input type="radio" name="operation" value="1"/> Sell <br>
            <label for="quantity">Quantity:&nbsp</label>
            <input type="number" min="1" id="quantity" name="quantity" size="10"/><br>
            <input type="submit" id="new_order" name="new_order" value="Order now!"/>
        </form>
        <p class="error" id="message"></p>
        <?php
        if (isset($neworder) && $neworder === -1) { ?>
            <p class="error">There are not enought stocks!</p>
            <?php
        } else if (isset($neworder) && $neworder === -2) { ?>
            <p class="error">You have not enought money to perform this operation!</p>
            <?php
        } else if (isset($neworder) && $neworder === -3) { ?>
            <p class="error">You have not enought stocks to perform this operation!</p>
            <?php
        } else if (isset($neworder) && $neworder === -4) { ?>
            <p class="error">There are not enought stocks!</p>
            <?php
        } else if (isset($neworder) && $neworder === 0) { ?>
            <p class="error">Something went wrong during the operation, please try again!</p>
            <?php
        } else if (isset($neworder) && $neworder === 1) { ?>
            <p class="success">The order has been successfully performed.</p>
            <?php
        }
        generate_books_table() ?>
    </div>
    <?php
} else {
    header("Location: login.php");
}
include('footer.php');
?>
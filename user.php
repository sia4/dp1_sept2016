<?php
require_once('header.php');

if(logged()) {
    ?>

    <div id="container">
        <h2>Personal Information</h2>
        <?php
        $user = get_user_info(get_username());
        ?>

        <table>
            <tr>
                <td class="el_name">Name:</td>
                <td><?php echo $user['name'] ?></td>
            </tr>
            <tr>
                <td class="el_name">Surname:</td>
                <td><?php echo $user['surname'] ?></td>
            </tr>
            <tr>
                <td class="el_name">Email:</td>
                <td><?php echo $user['email'] ?></td>
            </tr>
            <tr>
                <td class="el_name">Total Amount:</td>
                <td><?php echo $user['actual_amount'] . "€" ?></td>
            </tr>
            <tr>
                <td class="el_name"># Stocks:</td>
                <td><?php echo $user['n_stocks'] ?></td>
            </tr>
        </table>

        <?php
        $history = get_user_history(get_username());

        if ($history != false) {
            ?>
            <h2>History of trades made so far:</h2>
            <table class="data" id="history">
                <tbody>
                <tr class="header">
                    <th>Date and Time</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Operation</th>
                </tr>
                <?php
                foreach ($history as $h) {
                    ?>
                    <tr>
                        <td><?php echo $h['time'] ?></td>
                        <td><?php echo $h['quantity'] ?></td>
                        <td><?php echo $h['total_price'] ?></td>
                        <td><?php if ($h['buy_sell_flag'] == 0) echo "Buy"; else echo "Sell"; ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php
        }
        ?>

        <?php generate_books_table() ?>
    </div>

    <?php
} else {
        header("Location: login.php");
    }
include('footer.php');
?>
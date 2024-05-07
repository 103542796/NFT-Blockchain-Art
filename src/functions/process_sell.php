<?php
session_start();
require_once("../user_info/settings.php");

if (isset($_POST["sell-prod"])) {
    if (isset($_POST["id"])) {
        $prod_id = mysqli_real_escape_string($conn, $_POST["id"]);

        // This sql selects the user fields in which the user is the one who is logging in 
        // and the product that is chosen is owned by that user and it is the product which is selected
        $sql_seller = "SELECT * FROM users 
        INNER JOIN ownedProd ON users.user_id = ownedProd.user_id
        WHERE (users.user_id = '{$_SESSION['uid']}' 
        AND ownedProd.prod_id = '$prod_id' )";

        $query_seller = mysqli_query($conn, $sql_seller);
        $seller_data = mysqli_fetch_assoc($query_seller);
        $_SESSION['owner_to_be_seller_id'] = $seller_data['user_id'];
        $_SESSION['owner_to_be_seller_name'] = $seller_data['profile_name'];
        $_SESSION['prod_id_to_sell'] = $prod_id;

        include("../all_page/header.php");
        include("../all_page/body.php");

        if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) { // Validate if login successful or not
            header("location: ../user_info/login.php"); // If not, redirect to the login page.
            exit();
        }

        ?>
        <link href="../../style/childpage.css" rel="stylesheet">
        <link href="../../style/upload.css" rel="stylesheet">
        <!-- When the user sells a product, it will create another form which may be used for changing the price  -->
        <div class="main">
            <h2> Sell NFT </h2>
            <div class="form-upload">
                <form method="POST" action="process_sell_to_upload.php" enctype="multipart/form-data" id="myform">
                    <div class="product-field">
                        <label for="price">Change the Price</label><br>
                        <input type="text" id="price" name="price" placeholder="How many SeggCoin?"><br>
                    </div>

                    <button class="form-btn" name="upload" id="submit">Upload</button>
                </form>
            </div>
        </div>
    <?php

    include("../all_page/footer.php");
    }
}
?>
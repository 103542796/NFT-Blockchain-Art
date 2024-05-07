<?php
    session_start();    
    require_once("../user_info/settings.php");
    include("../functions/functions.php");

    // This sql select the product fields which match: the product_id = product that you want to sell
    // A session stored is used to pass to this page 
    $sql = "SELECT * FROM product WHERE product.prod_id = {$_SESSION['prod_id_to_sell']} ";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) 
    {
        $image = $row["image"];
        $signature = $row["signature_img"];
        $name = $row["prod_name"];
        $attribute = $row["attribute"];
        $desc = $row["description"];
    }
    $errMsg = array(); // Error handling

    if (isset($_POST["upload"])) {
        if (isset($_POST["price"]) && $_POST["price"] != "") {
            if (!preg_match('/^[+]?\d*\.?\d+$/', $_POST["price"])) {
                array_push($errMsg, "You must enter a positive number!"); // Store error in array
            } 
            else {
                $price = $_POST["price"];
            }
        } 
        else 
        {
            array_push($errMsg, "Price must not be empty!");  // Store error in array
        }

        if (empty($errMsg)) // If no error
        {
            $time_zone = new DateTimeZone('Australia/Sydney');
            $date = new DateTime('now', $time_zone);
            $current_time = $date->format("Y-m-d H:i:s");
            require_once("../user_info/settings.php");

            if ($conn) {
                $seller = $_SESSION['owner_to_be_seller_name'];
                $nft_name = mysqli_real_escape_string($conn, $name);
                $price = mysqli_real_escape_string($conn, $price);

                // This sql create a copy of the item you owned and sell. But it will update the price
                // The item will be inserted into product table
                $query_insert = "INSERT INTO product (prod_name, image, signature_img, seller_name, price, attribute, description, date_created)
                VALUES('$nft_name', '$image', '$signature', '$seller', '$price','$attribute', '$desc', '$current_time')";

                $insert = mysqli_query($conn, $query_insert);

                if ($insert) {
                    move_uploaded_file($image_tmp, '../../image/' . $image);
                    $prod_id = mysqli_insert_id($conn);
                    $email = $_SESSION["email"];

                    // This sql create uploading relationship between the item and the user who sell it. Similar to when he/she uploads an item
                    // The relationship will be inserted into dataProd table
                    $query_dataProd = "INSERT INTO dataProd (user_id, prod_id) VALUES (
                    (SELECT user_id FROM users WHERE email = '$email'), '$prod_id')";

                    $insert_dataProd = mysqli_query($conn, $query_dataProd);
                    if ($insert_dataProd) {
                        header("location: ../main/discoverpage.php");
                    } else {
                        array_push($errMsg, "Cannot Sell Work!");  // Store error in array
                    }
                } else {
                    array_push($errMsg, "Cannot Sell Work!");  // Store error in array
                }
            }
            mysqli_close($conn);
    } 
    else {
            echo ShowErrorMsg($errMsg); // Show errors
    }
    }
?>
<?php
    $id = $_POST["id"];
    $buy_prod = $_POST["buy_prod"];
    $response = array();
    
    session_start();
    require_once("../user_info/settings.php");
    if(isset($buy_prod)) 
    {
        if(isset($id))
        {
            $prod_id = mysqli_real_escape_string($conn, $_POST["id"]);    
            if(!isset($_SESSION["login"]) || isset($_SESSION["login"]) !== true) // Validate log in
            {
                $response['error'] =  'You must Login First !'; // Store error in array 
            }
            else
            {
                // This sql selects the seller data from table users
                $sql_seller = "SELECT * FROM users WHERE (users.user_id = 
                (SELECT user_id FROM dataProd WHERE dataProd.prod_id = {$_SESSION['get_prod_id']}) 
                )";

                // This sql selects the buyer data from table users
                $sql_buyer = "SELECT * FROM users WHERE (users.user_id = '{$_SESSION['uid']}' )";  
                
                $query_seller = mysqli_query($conn, $sql_seller);   
                $query_buyer = mysqli_query($conn, $sql_buyer); 

                $seller_data = mysqli_fetch_assoc($query_seller);
                $buyer_data = mysqli_fetch_assoc($query_buyer);

                $_SESSION['seller_id'] = $seller_data['user_id'];
                $_SESSION['seller_name'] = $seller_data['profile_name'];
                $_SESSION['buyer_name'] = $buyer_data['profile_name'];
                
                $buyer_name = mysqli_real_escape_string($conn, $_SESSION['buyer_name']);
                $seller_name = mysqli_real_escape_string($conn, $_SESSION['seller_name']);

                $time_zone = new DateTimeZone('Australia/Sydney');
                $date = new DateTime('now', $time_zone);
                $current_time = $date->format("Y-m-d H:i:s");
                
                $price = number_format($_SESSION['price'], 2, '.', '');
                $uid = intval($_SESSION['uid']);
                $seller_id = intval($_SESSION['seller_id']);
                
                // This sql inserts the prod_id, user_id and date_buy into table ownedProd of the buyer
                $sql_insert_prod = "INSERT INTO ownedProd(prod_id, user_id, date_buy) 
                VALUES ('$prod_id', '$uid', '$current_time') ";
                
                if($query_insert = mysqli_query($conn, $sql_insert_prod))
                {
                    // This sql inserts history transaction data in the table history
                    $sql_insert_history = "INSERT INTO history(date_time, amount)
                    VALUES('$current_time', '$price')
                    ";

                    if(mysqli_query($conn, $sql_insert_history))
                    {
                        $history_id = mysqli_insert_id($conn);
                        // This sql inserts the data into myHistory of buyer 
                        $sql_insert_myHistory_buyer = "INSERT INTO myHistory(history_id, user_id, action, label)
                        VALUES ('$history_id', '$uid', 'BUY', '$seller_name')
                        ";

                        // This sql inserts the data into myHistory of seller 
                        $sql_insert_myHistory_seller = "INSERT INTO myHistory(history_id, user_id, action, label)
                        VALUES ('$history_id', '$seller_id', 'SELL', '$buyer_name')
                        ";

                        if(mysqli_query($conn, $sql_insert_myHistory_buyer) && mysqli_query($conn, $sql_insert_myHistory_seller))
                        {
                            $response['success'] = $history_id; // store success in array
                            $response['seller_id'] = $seller_id; // store seller_id in array
                            $response['time'] = $current_time; // store time in array
                        }
                        else
                        {
                            $response['error'] =  'One of the Users transaction has failed !'; // store error in array
                        }
                        
                    }
                    else
                    {
                        $response['error'] =  'Cannot make History transaction !'; // store error in array
                    }
                }
                else
                {
                    $response['error'] =  'Purchase Unsuccessful !'; // store error in array
                }
            }
        }
        else
        {
            $response['error'] =  'Product does not Exist !'; // store error in array
        }
    
    }
    mysqli_close($conn);
    echo json_encode($response); // This encode the array into json 
?>
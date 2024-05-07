<?php include("../all_page/header.php"); ?>
<link href="../../style/childpage.css" rel="stylesheet">
<link href="../../style/upload.css" rel="stylesheet">

<?php 
    include("../all_page/body.php"); 
    require_once("../user_info/settings.php");
    include("../functions/functions.php");
    session_start();

    if(!isset($_SESSION["login"]) || isset($_SESSION["login"]) !== true) // validate if login successful or not
    {
        header("location: ../user_info/login.php"); // if not header to index file so they must register or login first
        exit();
    }
?>

<div class="main"> 
    <h2> Upload My Work </h2>
    <div class="form-upload"> 
    <form method="POST" action="upload.php" enctype="multipart/form-data" id="myform"> 
        
        <div class="product-field"> 
            <label for="name"> Image Name </label> <br>
            <input type="text" id="name" name="name"> <br>

            <label for="image">Image file</label> <br>
            <input type="file" id="image" name="image" > <br>

            <label for="signature">Signature IMG </label> <br>
            <input type="file" id="signature" name="signature" > <br>

            <label for="price"> Price </label> <br>
            <input type="text" id="price" name="price" placeholder="How many SeggCoin ?"> <br>

            <label for="attribute"> Attributes </label> <br> 
            <div class="attribute"> 
                <label for="human"> Modern </label> 
                <input type="checkbox" id="modern" name="modern" value="Modern Art"> 
                <label for="ai"> AI Art </label> 
                <input type="checkbox" id="ai" name="ai" value="AI Art"> <br>
                <label for="painting"> Painting </label> 
                <input type="checkbox" id="painting" name="painting" value="Painting"> 
                <label for="expr"> Expressionism </label> 
                <input type="checkbox" id="expr" name="expr" value="Expressionism"> <br>
            </div>
        
            <label for="desc"> Description </label> <br>
            <textarea name="desc" form="myform" rows="4" cols="50"> </textarea> <br>
        </div>
        
        <button class="form-btn" name="upload" id="submit"> Upload </button>
    </form>
    </div>
    
</div>

<?php 
    $errMsg = array(); // Error handling

    if(isset($_POST["upload"]))
    {
        if(isset($_POST["name"]) && $_POST["name"] != "")
        {
            $nft_name = $_POST["name"];
        }
        else
        {
            array_push($errMsg, "NFT Name must not be empty !"); // Push error in array 
        }

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) 
        {
            $image = $_FILES["image"]["name"];
            $image_tmp = $_FILES["image"]["tmp_name"];
        } 
        else 
        {
            array_push($errMsg, "Please select a file to upload !"); // Push error in array 
        }

        if (isset($_FILES["signature"]) && $_FILES["signature"]["error"] == 0) 
        {
            $signature = $_FILES["signature"]["name"];
            $signature_tmp = $_FILES["signature"]["tmp_name"];
        } 
        else 
        {
            array_push($errMsg, "Please select a file to upload !"); // Push error in array 
        }

        if(isset($_POST["price"]) && $_POST["price"] != "")
        {
            if(!preg_match('/^[+]?\d*\.?\d+$/', $_POST["price"]))
            {
                array_push($errMsg, "You must enter a positive number !"); // Push error in array 
            }
            else
            {
                $price = $_POST["price"];
            }
        }
        else
        {
            array_push($errMsg, "Price must not be empty !"); // Push error in array 
        }

        if((isset($_POST["modern"]) && $_POST["modern"] != "") || (isset($_POST["ai"]) && $_POST["ai"] != "") 
        || (isset($_POST["painting"]) && $_POST["painting"] != "") || (isset($_POST["expr"]) && $_POST["expr"] != "" )) 
        {
            $selectedAttributes = [];
            
            if (isset($_POST["modern"])) {
                $selectedAttributes[] = $_POST["modern"];
            }
            
            if (isset($_POST["ai"])) {
                $selectedAttributes[] = $_POST["ai"];
            }
            
            if (isset($_POST["painting"])) {
                $selectedAttributes[] = $_POST["painting"];
            }
            
            if (isset($_POST["expr"])) {
                $selectedAttributes[] = $_POST["expr"];
            }
            
        }
        else
        {
            array_push($errMsg,"You must select at least 1 attribute"); // Push error in array 
        }

        if($errMsg == array()) // if no errors
        {
            $desc = $_POST["desc"];
            $time_zone = new DateTimeZone('Australia/Sydney');
            $date = new DateTime('now', $time_zone);
            $current_time = $date->format("Y-m-d H:i:s");
            require_once("../user_info/settings.php");
            
            if($conn)
            {
                $creator = $_SESSION["profile_name"];
                $u_id = $_SESSION['uid'];
                $nft_name = mysqli_real_escape_string($conn, $nft_name);
                $price = mysqli_real_escape_string($conn, $price);
                $selectedAttributesStr = implode(', ', $selectedAttributes);
                $timestamp = date("YmdHis");
                $image = $timestamp . "_" . $image;
                $signature = $u_id . "_" . $creator . "_" . $signature;
                
                //This sql insert the values into table product with the fields below
                $query_insert ="INSERT INTO product (prod_name, image, signature_img, seller_name, price, attribute, description, date_created)
                VALUES('$nft_name', '$image', '$signature', '$creator', '$price','$selectedAttributesStr', '$desc', '$current_time')";
                
                $insert = mysqli_query($conn,$query_insert);
                
                if($insert) //If successfully insert record into the database
                {
                    move_uploaded_file($image_tmp, '../../image/' .$image); // Store image in folder image
                    move_uploaded_file($signature_tmp, '../../signature/' .$signature); // Store signature in folder signature
                    $prod_id = mysqli_insert_id($conn);
                    $email = $_SESSION["email"];

                    // This sql insert into table dataProd the user_id of the user who uploads and the prod_id
                    $query_dataProd = "INSERT INTO dataProd (user_id, prod_id) VALUES (
                    (SELECT user_id FROM users WHERE email = '$email'), '$prod_id')";

                    $insert_dataProd = mysqli_query($conn,$query_dataProd);
                    if($insert_dataProd)
                    {
                        // If successfully insert into dataProd table, all are correct and location to discoverpage
                        ?>
                        <script>
                            window.location.href = 'http://localhost/cos30049-gr2-code/src/main/discoverpage.php';
                        </script>
                        <?php
                    }
                    else
                    {
                        array_push($errMsg, "Cannot Upload a new Work !"); // Push error in array 
                    }
                }
                else
                {
                    array_push($errMsg, "Cannot Upload a new Work !"); // Push error in array 
                }
            }
            mysqli_close($conn);
        }

        else // if error
        {
            echo ShowErrorMsg($errMsg); // Shows errors in the screen
        }
    }

?>
<?php include("../all_page/footer.php"); ?>
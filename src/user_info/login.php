<!-- Include the necessary files -->
<?php include("../all_page/header.php"); ?>
<link href = "../../style/childpage.css" rel="stylesheet">
<link href = "../../style/login.css" rel="stylesheet">
<?php include("../all_page/body.php"); ?>

<div class="main"> 
    <div class="form-upload"> 
    <!-- Login Form Start here -->
    <form method="POST" action="login.php"> 
	    <h1> Login </h1>
        <div class="login_field"> 
            <label for="mail"> Email:  </label> <br>
            <input type="text" id="email" name="email"> <br>

            <label for="user_pwd"> Password: </label> <br>
            <input type="password" id="password" name="pwd"> <br>
        </div>
        <br>
        <!-- Submit button before proceeding to itself  -->
        <input type="submit" name ="login" value="Log in" class="form-btn" id="submit"> <br>
        <a href = "signup.php" id="note"> Don't have an account? SignUp bruv</a>

    </form>
    
</div>

<?php
// set up the files
    session_start();
    require_once("../user_info/settings.php");  
    include('../functions/functions.php'); 
    $errMsg = array(); // Error handling
    // assign empty, use for the loops
    $email = ""; 
    $pwd = "";
   
    if(isset($_POST["login"]))
    {
        // check connection
        if (!$conn) {
            die("Unable to connect to the database!");
        }

        if(isset($_POST["email"]) && $_POST["email"] != "") // validate email input not empty
        {
            $email = $_POST["email"];
        }
        else
        {
            array_push($errMsg, "Email must not be empty, please Enter !"); // Push error in array 
        }

        if(isset($_POST["pwd"]) && $_POST["pwd"] != "") // validate pass not empty
        {
            $pwd = $_POST["pwd"];
        }
        else
        {
            array_push($errMsg, "Password must not be empty, please Enter !"); // Push error in array 
        }

        if($errMsg == array()) // if no error
        {
            require_once("../user_info/settings.php"); 
            if($conn)
            {
                $query = "SELECT * FROM users WHERE email='$email' "; 
                $result= mysqli_query($conn,$query);	
                $res1 = mysqli_num_rows($result);

                if($res1 == 1) // If email is registered
                {
                    $row = mysqli_fetch_assoc($result);
                    if($row["password"] == $pwd) // If password matches the registed pass
                    {
                        // assign the sessions and header to the discoverpage
                        $_SESSION['login'] = true;
                        $_SESSION['uid'] = $row['user_id'];
                        $_SESSION["email"] = $email;
                        $_SESSION['profile_name'] = $row["profile_name"];

                        header("location: ../main/discoverpage.php");
                    }
                    else // if pass not matches
                    {
                        array_push($errMsg, "Password does not match !"); // Push error in array 
                    }
                }
                else // If email not registered
                {
                    array_push($errMsg, "Email does not exist !");  // Push error in array 
                }
            }
            else
            {
                array_push($errMsg, "Cannot connect to the database !");  // Push error in array 
            }
            mysqli_close($conn);

        }

        echo ShowErrorMsg($errMsg); // Print the errors to the Main screen  
    }

?>

<?php include("../all_page/footer.php"); ?>
<?php include("../all_page/header.php"); ?>
<link href="../../style/childpage.css" rel="stylesheet">
<link href = "../../style/signup.css" rel="stylesheet">
<?php include("../all_page/body.php"); ?>

<div class="main"> 
    
    <div class="form-upload"> 
    <form method="POST" action="signup.php"> 
	    <h1> SignUp </h1>
        <div class="sign_up"> 
            <label for="price"> Email:  </label> <br>
            <input type="text" id="email" name="email"> <br>

            <label for="pfname">Profile Name:</label><br>
            <input type="text" id="user" name="pfname"><br>

            <label for="name"> Password: </label> <br>
            <input type="password" id="password" name="pwd"> <br>
			
            <label for="name"> ReEnter Password: </label> <br>
            <input type="password" id="password1" name="pwd1"> <br>
        </div>
        <br>
            <input type="submit" name ="register" class="form-btn" id="submit" value="Register">
            <input type= "reset" name="reset" class="form-btn" id="reset">
        <br>
        <a href = "login.php" id="note"> Have an account? Login bruv</a>

    </form>
</div>
<?php
    include('../functions/functions.php');
    $errMsg = array(); // Error handling
    require_once("../user_info/settings.php");

    if(isset($_POST["register"]))
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
        
        if(isset($_POST["email"]) && $_POST["email"] != "") //check email input
        {
            if(!preg_match($regex, $_POST["email"])) // validate email format
            {
                array_push($errMsg, "Email is invalid form !"); // Push error in array 
            }
            else
            {
                $email = $_POST["email"];
                $query = "SELECT email FROM users WHERE email='$email' ";
                $result= mysqli_query($conn,$query);	
                $row = mysqli_num_rows($result);
                if($row >0)
                {
                    array_push($errMsg, "An account with the Email already exists !"); // Push error in array 
                }
            
            }
        }
        else // error not input
        {
            array_push($errMsg, "Email cannot be empty !"); // Push error in array 
        }

        if(isset($_POST["pfname"]) && $_POST["pfname"] != "") // check pf name input
        {
            if(!preg_match('/^[a-zA-Z ]+$/', $_POST["pfname"])) // validate pf name Letter only
            {
                array_push($errMsg, "Profile name must only contains letter !"); // Push error in array 
            }
            elseif(strlen($_POST["pfname"]) > 30)
            {
                array_push($errMsg, "The maximum length of Profile name is 30 characters !"); // Push error in array 
            }
            else
            {
                $pfname = $_POST["pfname"];
            }
        }
        else // error not input
        {
            array_push($errMsg, "Profile name cannot be empty !"); // Push error in array 
        }

        if(isset($_POST["pwd"]) && $_POST["pwd"] != "") // validate pass
        {
            if(!preg_match('/^[a-zA-Z0-9]+$/',$_POST["pwd"])) // if not only contains words and numbers
            {
                array_push($errMsg, "The password must contains both letters and numbers !"); // Push error in array 
            }
            else
            {
                $pwd = $_POST["pwd"];
            }
        }
        else
        {
            array_push($errMsg, "Password must not be empty !"); // Push error in array 
        }

        if(isset($_POST["pwd1"]) && $_POST["pwd1"] != "") // validate confirm pass
        {
            if( $_POST["pwd1"] !== $_POST["pwd"]) // if pass and confirm pass not match
            {
                array_push($errMsg, "Confirm password does not match your password !"); // Push error in array 
            }
            else
            {
                $pwd1 = $_POST["pwd1"];
            }
        }
        else
        {
            array_push($errMsg, "Please enter confirm password !"); // Push error in array 
        }

        if($errMsg == array()) //If no error
        {   
            require_once("../user_info/settings.php");

            if($conn)
            {
                // Insert primitive data 
                $query_insert ="INSERT INTO users (email, password, profile_name)
                VALUES('$email','$pwd','$pfname')";
                
                $insert = mysqli_query($conn,$query_insert);

                if($insert) // if only successful insert before
                {
                    header("Location: login.php");
                }
                else
                {
                    array_push($errMsg, "Fail to register !"); // Push error in array 
                }	
            }
            mysqli_close($conn);
        }

        else
        {
            echo ShowErrorMsg($errMsg); // Shows errors in the screen 
        }
    }

?>

<?php include("../all_page/footer.php"); ?>
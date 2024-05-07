<?php 
    require dirname(__FILE__). '/src/all_page/header.php'; // Include the files: Header, childpage, body
?>
<link href="http://localhost/cos30049-gr2-code/style/childpage.css" rel="stylesheet">  
<?php 
    require dirname(__FILE__). '/src/all_page/body.php';
?>

<div class="main">
    <?php
    // check connection
    require dirname(__FILE__). '/src/user_info/settings.php';
        $conn = @new mysqli($host, $user, $pswd, $dbnm);

        if ($conn->connect_error) {
            die("Unable to connect to the database server" . $conn->connect_error);
        }

        echo "<p class='nofi'> Successfully connected to the database </p>";

    // create table users if not exists
        $query = "CREATE TABLE IF NOT EXISTS users(
            user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(50) NOT NULL,
            password VARCHAR(20) NOT NULL,
            profile_name VARCHAR(30) NOT NULL
        )";

        if ($conn->query($query) === TRUE) // validate create table friends
        {
            echo "<p class='nofi'> Table 'users' created successfully! </p>";
        } 
        else 
        {
            echo "<p class='nofi'> Cannot create table 'users' ! ".$conn->error." </p>";
        }

    // create table product 
        $query1 = "CREATE TABLE IF NOT EXISTS product(
            prod_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            image VARCHAR(200) NOT NULL,
            signature_img VARCHAR(200) NOT NULL,
            prod_name VARCHAR(100) NOT NULL,
            seller_name VARCHAR(30) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            attribute VARCHAR(100) NOT NULL,
            description VARCHAR(500) NOT NULL,
            date_created DATETIME NOT NULL
        )";
    
        if ($conn->query($query1) === TRUE) // validate create table myfriends
        {
            echo "<p class='nofi'> Table 'product' created successfully! </p>";
        } 
        else 
        {
            echo "<p class='nofi'> Cannot create table 'product' ! ".$conn->error." </p>";
        }

    // create table dataProd
        $query2 = "CREATE TABLE IF NOT EXISTS dataProd(
            prod_id INT NOT NULL, 
            user_id INT NOT NULL,
            FOREIGN KEY (prod_id) REFERENCES product(prod_id),
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        )";
    
        if ($conn->query($query2) === TRUE) // validate create table myfriends
        {
            echo "<p class='nofi'> Table 'dataProd' created successfully! </p>";
        } 
        else 
        {
            echo "<p class='nofi'> Cannot create table 'dataProd' ! ".$conn->error." </p>";
        }

    // create table history
        $query3 = "CREATE TABLE IF NOT EXISTS history(
            history_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            date_time DATETIME NOT NULL,
            amount DECIMAL(10, 2) NOT NULL
        )";

        if ($conn->query($query3) === TRUE) // validate create table myfriends
        {
            echo "<p class='nofi'> Table 'history' created successfully! </p>";
        } 
        else 
        {
            echo "<p class='nofi'> Cannot create table 'history' ! ".$conn->error." </p>";
        }

    // create table myHistory
        $query4 = "CREATE TABLE IF NOT EXISTS myHistory(
            history_id INT NOT NULL, 
            user_id INT NOT NULL,
            action VARCHAR(20) NOT NULL,
            label VARCHAR(50) NOT NULL,
            FOREIGN KEY (history_id) REFERENCES history(history_id),
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        )";

        if ($conn->query($query4) === TRUE) // validate create table myfriends
        {
            echo "<p class='nofi'> Table 'myHistory' created successfully! </p>";
        } 
        else 
        {
            echo "<p class='nofi'> Cannot create table 'myHistory' ! ".$conn->error." </p>";
        }

    // create table ownedProd
        $query5 = "CREATE TABLE IF NOT EXISTS ownedProd(
            prod_id INT NOT NULL,
            user_id INT NOT NULL,
            date_buy DATETIME NOT NULL,
            FOREIGN KEY (prod_id) REFERENCES product(prod_id),
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        )";

        if ($conn->query($query5) === TRUE) // validate create table myfriends
        {
            echo "<p class='nofi'> Table 'ownedProd' created successfully! </p>";
        } 
        else 
        {
            echo "<p class='nofi'> Cannot create table 'ownedProd' ! ".$conn->error." </p>";
        }

        $conn->close();
        
    ?>
</div>




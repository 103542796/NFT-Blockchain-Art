<?php include("../all_page/header.php"); ?>

<link href="../../style/childpage.css" rel="stylesheet">

<?php include("../all_page/body.php") ?>

<?php 
    require_once("settings.php");
    session_start();

    if(!isset($_SESSION["login"]) || isset($_SESSION["login"]) !== true) // validate if login successful or not
    {
        header("location: login.php"); // if not header to index file so they must register or login first
        exit();
    }
?>

<div class="main">
    <?php
        require_once("settings.php");
        $sql = "SELECT * FROM users WHERE users.user_id = '{$_SESSION['uid']}'";
        $query = mysqli_query($conn, $sql);

        while($row = mysqli_fetch_assoc($query)) {
            $sql_tb_product = "SELECT COUNT(*) AS RecordCount FROM dataProd
            INNER JOIN product ON product.prod_id = dataProd.prod_id
            WHERE dataProd.user_id = '{$_SESSION['uid']}'
            ";
            $query_tb_product = mysqli_query($conn, $sql_tb_product);

            if ($row_tb_product = mysqli_fetch_assoc($query_tb_product)) {
                $nft_count = $row_tb_product['RecordCount'];
            } 
            else {
                $nft_count = 0; // If no NFTs are found
            }

            ?>
            <!-- The Div shows information retrieved from teh DB and Blockchain -->
            <div class="user_panel" onload="checkWallet()">
                <p> Your Name: <?php echo $row["profile_name"] ?> </p>
                <p> Your Number of NFTs: <?php echo $nft_count ?> NFTs </p>
                <p> Your Wallet Address: <span id="address"> </span> </p>
                <p> Your Wallet Balance: <span id="balance"> </span> Segg Coins </p>
            </div>

            <script>
                async function checkWallet() {
                const web3 = new Web3("http://3.91.43.154:8545"); // Take the URL of AWS instance
                const accounts = await web3.eth.getAccounts(); // Get Account address
                const balance = await web3.eth.getBalance(accounts[0]); // Get Balance of the account
               
                var web3Provider = new Web3.providers.HttpProvider("http://3.91.43.154:8545"); //Provider also take the URL of AWS Instance
 
                // get Json file that is compiled from the Contract 
                $.getJSON("http://localhost/cos30049-gr2-code/src/main/Group2.json", function(data){
                    var FriendArtifact = data;
                    var FriendContract = TruffleContract(FriendArtifact);
                    FriendContract.setProvider(web3Provider);
                    var friendinstance;
 
                    FriendContract.deployed().then(function(instance){
                        var address = accounts[0]; // Assign variable address with account address 
                        var wallet_balance = balance; // Assign variable wallet_balance with account balance
                        document.getElementById("address").innerHTML = address; // Shows address in HTML
                        document.getElementById("balance").innerHTML = wallet_balance; // Shows balance in HTML
                    });
                })
            }
            document.addEventListener("DOMContentLoaded", checkWallet);
            </script>
            <?php
        }
    ?>
</div>
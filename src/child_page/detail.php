<?php
    include("../all_page/header.php");
    require_once("../user_info/settings.php");
?>

<link href="../../style/childpage.css" rel="stylesheet"> 
<link href="../../style/detail.css" rel="stylesheet"> 

<?php include("../all_page/body.php") ?>
<script>
    // This function shows the Description field
    function showDescription() {
        var descField = document.getElementById("desc-field");
        var attributeField = document.getElementById("attribute-field");
        
        // Show the description field and hide the attribute field
        descField.style.display = "block";
        attributeField.style.display = "none";
    }

    // This function shows the Attribute field
    function showAttribute() {
        var descField = document.getElementById("desc-field");
        var attributeField = document.getElementById("attribute-field");

        // Show the attribute field and hide the description field
        attributeField.style.display = "block";
        descField.style.display = "none";
    }
</script>

<div class="main"> 
    <?php 
        session_start();
        $get_id = $_GET['id'];
        $_SESSION['get_prod_id'] = $get_id;
        
        // This sql select the product which match the prod_id is the id it gets from choosing in the discoverpage
        $sql_detail = "SELECT * FROM product WHERE product.prod_id= '{$_GET['id']}' LIMIT 1 ";
        $query_detail = mysqli_query($conn, $sql_detail);
        
        while ($row_detail = mysqli_fetch_array($query_detail)) {
        ?>
            <div class="featured"> 
                <img src="../../image/<?php echo $row_detail["image"] ?>" class="cover"> 
            </div>
            <div class="info">
                <h3><?php echo $row_detail["prod_name"]?></h3>
                <p> <strong> Seller: <?php echo $row_detail["seller_name"] ?> </strong></p>
                <p> <strong> Date Publish: <?php echo $row_detail["date_created"] ?> </strong></p>
                <p> <strong> Price: <?php echo $row_detail["price"] ?> </strong></p>
                <?php
                    $sql_num_buy = "SELECT COUNT(prod_id) FROM ownedProd
                    WHERE ownedProd.prod_id = '{$_GET['id']}'
                    ";
                    $query_num_buy = mysqli_query($conn, $sql_num_buy);

                    if ($query_num_buy) {
                        $row = mysqli_fetch_row($query_num_buy);
                        $count = $row[0];
                    } else {
                        $count = 0; 
                    }

                    ?>
                        <p> <strong> Number of Purchases: <?php echo $count; ?> </strong> </p>
                    <?php
                ?>
                <?php $_SESSION["price"] = $row_detail["price"]; ?>

                <form id="form-buy">
                    <input type="hidden" id="id_prod" name="id" value="<?php echo $_GET['id']; ?>">
                    <i class="bi bi-wallet2"></i>
                    <input type="submit" value="Buy Now" id="buy-btn" name="buy-prod"> 
                    <i class="bi bi-arrow-right"> </i>
                </form>

                <div id="response"> </div>

                <script>
                    // API starts 
                    $(document).ready(function() {
                        // Getting id from the above form 
                        $('#form-buy').submit(function(e) {
                            e.preventDefault();
                            // Assig variables with the ids of the Buy button and Id of the product
                            var id_prod = $('#id_prod').val(),
                                buy_prod = $('#buy-btn').val();

                            $.ajax({
                                // Ajax API url to the process_buy.php
                                url: 'http://localhost/cos30049-gr2-code/src/functions/process_buy.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { 
                                    // Assign name (id) and (buy_prod) with above variables
                                    id: id_prod,
                                    buy_prod: buy_prod 
                                },
                                success: function(response) {
                                    if(response['success']) // if the response success is get from process_buy
                                    {
                                        // Assign variables
                                        $uid = <?php echo $_SESSION['uid']; ?>;
                                        $seller_id = response['seller_id'];
                                        $time = response['time'];
                                        $para_load = response['success'];
                                        loadWeb3($para_load, $uid, $seller_id); // Function Smart Contract
                                        alert("Purchased Successful ! Click OK"); // Alert if success
                                    }
                                    else if(response['error']) // if response error is get from process_buy
                                    {
                                        alert("Alert: " + response['error']); // Alert the errors 
                                    }
                                }
                            });
                        });
                    });

                    // Implement Smart Contract here
                    async function loadWeb3($para_load, $uid, $seller_id, callback) {
                        const web3 = new Web3("http://3.91.43.154:8545"); // AWS Instance
                        const accounts = await web3.eth.getAccounts(); // Get Ethereum account address 

                        var history_id = $para_load;
                        var uid = $uid;
                        var seller_id = $seller_id;
                        var time = $time;

                        console.log(accounts[0]);

                        buyMintDataString = history_id + "," + time + "," + uid + "," + 'BUY' + "," + accounts[0];
                        sellMintDataString = history_id + "," + time + "," + seller_id + "," + 'SELL' + "," + accounts[0];
                        
                        console.log(buyMintDataString); // Console the mint message
                        console.log(sellMintDataString); // Console the mint message
                        var web3Provider = new Web3.providers.HttpProvider("http://3.91.43.154:8545"); // AWS Instance

                        $.getJSON("http://localhost/cos30049-gr2-code/src/main/Group2.json", function(data){
                            var NFTArtifact = data;
                            var NFTContract = TruffleContract(NFTArtifact);
                            NFTContract.setProvider(web3Provider);
                            var NFTInstance;

                            NFTContract.deployed().then(function(instance){
                                // Minting the data into Blockchain
                                NFTInstance = instance;
                                NFTInstance.mint(buyMintDataString, {from: '0x9C1ffE2a09F7f001cC782B58f5EFf1b0Fe27642c'});
                                NFTInstance.mint(sellMintDataString, {from: '0x9C1ffE2a09F7f001cC782B58f5EFf1b0Fe27642c'});

                            });
                        });
                    
                    }
                </script>
            </div>
        
            <div class="detail-btn"> 
                <div class="description">
                    <button onclick="showDescription()"> Description </button>
                </div>
                <div class="attribute">
                    <button onclick="showAttribute()"> Attribute </button>
                </div>
            </div>

            <div id="desc-field" class="eff-btn"> 
                <p> <?php echo $row_detail["description"] ?> </p>
            </div>

            <div id="attribute-field" class="eff-btn"> 
                <?php
                    $selectedAttributes = $row_detail["attribute"];
                    $selectedAttributes_exp = explode(",", $selectedAttributes);
                    foreach( $selectedAttributes_exp as $attribute ) {
                        ?>
                            <p> Attribute: <?php echo $attribute ?> </p>
                        <?php
                    }
                ?>
            </div>

        </div>
        <?php
        }
    ?>
        
</div>

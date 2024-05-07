<?php
session_start();
require_once ("db_connect.php");
$un = $_SESSION['username'];

$sql = "SELECT * FROM orders WHERE username = '$un' AND order_status = 'IN CART'";
$result = mysqli_query($conn, $sql);

// Fetch the existing date and item id from the database
$scsql = "SELECT item_id, date FROM orders WHERE order_status = 'IN CART' AND username = '$un'";
$order = mysqli_query($conn, $scsql);

$sc = [];
$totalPrice = 0;
$itemnum = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $item_id = $row['item_id'];
    $pokemon_query = "SELECT name, price FROM items WHERE itemid = '$item_id'";
    $pokemon_result = mysqli_query($conn, $pokemon_query);
    $pokemon_row = mysqli_fetch_assoc($pokemon_result);
    $productName = $pokemon_row['name'];
    $dollarPrice = $pokemon_row['price'];
    $coinPrice = ccp($dollarPrice);
    $itemnum += 1;
    $sc[] = [$productName, $dollarPrice, $coinPrice];
    $totalPrice += $dollarPrice;
}

function ccp($dp)
{
    return $dp * 2;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset ($_POST['ct'])) {
        $errorMsg = "Please generate a smart contract and agree with it.";
    } else {
        // Check if the user wants to use a new address or the existing one
        if (isset ($_POST['address_option']) && $_POST['address_option'] == 'new_address') {
            $new_address = $_POST['address'];
            $postcode = $_POST['postcode'];
            $phone = $_POST['phone'];
            $address = $new_address . ', ' . $postcode . ', ' . $phone;
        } else {
            // Fetch the existing address from the database
            $stmt = $conn->prepare("SELECT address FROM account WHERE username = ?");
            if ($stmt === false) {
                die ('prepare() failed: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("s", $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $address = $user['address'];

        }

        // Update the orders with the address and set the status to "PURCHASED"
        $stmt = $conn->prepare("UPDATE orders SET address = ?, order_status = 'PURCHASED' WHERE username = ? AND order_status = 'IN CART'");
        if ($stmt === false) {
            die ('prepare() failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ss", $address, $_SESSION['username']);
        $stmt->execute();

        exit();
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt and Payment Form</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="css/payment.css">
    <script src="http://localhost/COS30049/blockchain/web3.min.js"></script>
    <script src="http://localhost/COS30049/blockchain/truffle-contract.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <nav class="navbar" id="navigationBar">
        <a href="index.php" class="logo"><img src="images/pokeball.png">AstraTrade.com</a>
        <a href="market.php">Market</a>
        <a href="astracoin.php">AstraCoin</a>
        <?php
        if (isset ($_SESSION['user_id'])) {
            echo '<div class="split">';
            echo '<a href="wallet.php">' . $_SESSION['user_coin'] . ' AstraCoin</a>';
            echo '<a href="personal.php">' . $_SESSION['username'] . '</a>';
            echo '</div>';
        } else {
            echo '<div class="split">';
            echo '<a href="signup.php">Sign Up</a>';
            echo '<a href="login.php">Log In</a>';
            echo '</div>';
        }
        ?>
        <a href="javascript:void(0);" class="menu" onclick="navResponsive()">
            <i class="fa fa-bars"></i>
        </a>
    </nav>
    <section id="receiptContainer">
        <h2 class="title">Thanks for your order! Here are your Items!</h2>
        <section id="receiptContent" style="font-family: 'Garamond', sans-serif;">
            <table>
                <thead>
                    <tr>
                        <td></td>
                        <th>Product</th>
                        <th id="priceHeader">Product Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($sc as $item) {
                        $productName = $item[0];
                        $dollarPrice = $item[1];
                        $coinPrice = $item[2];
                        echo "<tr>
                                    <td></td>
                                    <td>$productName</td>
                                    <td class='price-column'>
                                        <span class='coin-price'> $coinPrice (AstraCoin)</span>
                                    </td>
                                </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </section>
    <div id="paymentContainer">
        <div id="shipmentDetails">
            <h1>Shipment Details</h1>
            <fieldset>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
                <label for="postcode">Postcode:</label>
                <input type="text" id="postcode" name="postcode" required>
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required>
                <div class="address-selection">
                    <label>Select Address:</label>
                    <div class="address-options">
                        <label for="use_existing_address">
                            <input type="radio" id="use_existing_address" name="address_option" value="existing_address"
                                checked>
                            Use Existing Address
                        </label>
                        <label for="use_new_address">
                            <input type="radio" id="use_new_address" name="address_option" value="new_address">
                            Use New Address
                        </label>
                    </div>
            </fieldset>

        </div>

        <h1 id="paymentTitle">Payment details</h1>
        <form action="" method="post" id="purchase-form" style="font-family: 'Garamond', sans-serif;">
            <fieldset>
                <p>Buyer:
                    <?= $un ?>
                </p>
                <p>Total items:
                    <?= $itemnum ?>
                </p>
                <p>Total price:
                    <?= ccp($totalPrice) ?>
                </p>
                <p>Transaction fee:
                    <?= ccp($totalPrice) * 0.05 ?>
                </p>
                <div id="generateContractBtn" style="text-align: center; margin-top: 20px;">
                    <button type="button" id="get-receipt-btn" onclick="f1()">Get receipt</button>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" id="submit-btn">Submit Payment</button>
                </div>
                <div id="overlay"
                    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center;">
                    <div id="contractModal"
                        style="font-size: 14px; text-align: left; max-height: 80vh; overflow-y: auto;">
                        <button id="closeContractBtn" onclick="f6()" style="float: right;">X</button>
                        <h2 style="font-size: 16px;">Receipt details:</h2>
                        <p>Seller:
                            <?= $un ?>
                        </p>
                        <p>Buyer:
                            <?= $un ?>
                        </p>
                        <ul style="text-align: left; list-style: none; padding: 0; margin-left: 15px;">
                            <?php foreach ($sc as $item) { ?>
                                <li style="margin-bottom: 3px;">
                                    <?= $item[0] ?>
                                </li>
                            <?php } ?>
                        </ul>
                        <p>Total Price: $
                            <?= $totalPrice ?>
                        </p>
                        <p style="font-size: 14px;">Card Type:
                            <?= implode(', ', array_column($sc, 0)) ?>
                        </p>
                        <h3 style="font-size: 14px;"> Transaction Fees:</h3>
                        <p style="font-size: 14px;">AstraTrade charges a premium and commission of 5% (
                            <?= ccp($totalPrice) * 0.05 ?>) based on the transaction value.
                        </p>
                        <p style="font-size: 14px;">The calculated transaction fee is $
                            <?= $totalPrice * 1.05 ?>
                        </p>
                        <div
                            style="display: flex;justify-content: space-between; align-items: flex-start; margin-top: 10px;float:left; max-width:100%;">
                            <input type="checkbox" id="contractAgreeTermsCheckbox" name="ct"
                                style="padding: 0;margin:0;">
                            <label for="contractAgreeTermsCheckbox">I agree to proceed with the transaction</label>
                        </div>
                        <button type="button" id="finalizeAgreementBtn"
                            style="margin-top: 60px; font-size: 14px; padding: 12px 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; display: none;"
                            onclick="f2()">Confirm</button>
                    </div>
                </div>
            </fieldset>
        </form>
        <script>
            $(document).ready(function () {
                $('#purchase-form').submit(function (e) {
                    e.preventDefault();

                    var username = '<?php echo $_SESSION['username']; ?>';

                    <?php
                    while ($scrow = mysqli_fetch_assoc($order)) {
                        ?>
                        var scitem_id = '<?php echo $scrow['item_id']; ?>';
                        var sctime = '<?php echo $scrow['date']; ?>';
                        loadWeb3(username, scitem_id, sctime); // Function Smart Contract
                        <?php
                    }
                    ?>
                });
            });

            async function loadWeb3(username, scitem_id, sctime) {
                const web3 = new Web3("http://3.249.227.203:8545");
                const accounts = await web3.eth.getAccounts();

                console.log(accounts[0]);

                var purchaseMintData = [scitem_id, sctime, username, accounts[0]];

                console.log(purchaseMintData); // Log the mint data

                $.getJSON("http://localhost/COS30049/blockchain/Color.json", function (data) {
                    var NFTArtifact = data;
                    var NFTContract = TruffleContract(NFTArtifact);
                    var web3Provider = new Web3.providers.HttpProvider("http://3.249.227.203:8545");

                    NFTContract.setProvider(web3Provider);

                    NFTContract.deployed().then(function (instance) {
                        // Minting the data into Blockchain
                        instance.mint(purchaseMintData, { from: accounts[0] })
                            .then(function (result) {
                                alert("Purchase Successful! Click OK");
                                console.log(result); // Log the transaction result
                            })
                            .catch(function (error) {
                                console.error(error); // Log any errors
                                alert("Purchase failed: " + error.message);
                            });
                    });
                });
            }
        </script>
    </div>
    <footer>
        <div class="footer-left"></div>
        <div class="footer-body">
            <div class="links">
                <h3>Links</h3>
                <a class="smart-link" href="#">F.A.Q.</a>
                <a class="smart-link" href="#">Contact Us</a>
                <a class="smart-link" href="#">Term of Service</a>
            </div>
            <div class="links">
                <h3>Follow us on</h3>
                <a class="smartlink" href="#"><i class="fa-brands fa-discord"></i>Discord</a>
                <a class="smartlink" href="#"><i class="fa fa-twitter fa-fw"></i>Twitter/X</a>
                <a class="smartlink" href="#"><i class="fa fa-facebook fa-fw"></i>Facebook</a>
            </div>
            <div class="links">
                <h3>Privacy</h3>
                <a class="smartlink" href="#"></i>Policy</a>
            </div>
        </div>
        <div class="footer-right"></div>
    </footer>
</body>
<script src="js/payment.js"></script>
<script src="js/index.js"></script>

</html>
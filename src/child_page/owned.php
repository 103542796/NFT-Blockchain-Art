<?php
    include("../all_page/header.php");
    require_once("../user_info/settings.php");
?>

<link href="../../style/childpage.css" rel="stylesheet"> 
<link href="../../style/detail.css" rel="stylesheet"> 

<?php include("../all_page/body.php") ?>
<script>
    function showDescription() {
        var descField = document.getElementById("desc-field");
        var attributeField = document.getElementById("attribute-field");
        
        // Show the description field and hide the attribute field
        descField.style.display = "block";
        attributeField.style.display = "none";
    }

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
    // This sql selects the product with id = id get from mycollection.php
        $sql_detail = "SELECT * FROM product WHERE product.prod_id= '$_GET[id]' LIMIT 1 ";
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
                
                <form id="form-buy" method="POST" action="../functions/process_sell.php">
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                    <i class="bi bi-wallet2"></i>
                    <input type="submit" value="Sell" id="buy-btn" name="sell-prod"> 
                    <i class="bi bi-arrow-right"></i>
                </form>
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
<!-- Include the necessary files  -->
<?php include("../all_page/header.php"); ?>

<link href="../../style/childpage.css" rel="stylesheet">
<link href="../../style/discoverpage.css" rel="stylesheet">
<link href="../../style/slider.css" rel="stylesheet">

<?php include("../all_page/body.php") ?>

<?php 
    require_once("../user_info/settings.php");
    include("../functions/functions.php");
?>

<div class="main"> 

<div class="container2">
    <div class="left-column2">
        <button type="button" onclick="toggleOptions()" id="filter-btn"> <i class="bi bi-funnel"> </i> Sort by Attribute </button>
        <div id="options" style="display: none;">
            <input type="checkbox" name="option1" value="../child_page/modernart.php"> Modern
            <input type="checkbox" name="option2" value="../child_page/aiart.php"> AI Art
            <input type="checkbox" name="option3" value="../child_page/painting.php"> Painting
            <input type="checkbox" name="option4" value="../child_page/expressionism.php"> Expressionism
        </div>
    </div>
</div>

<script>
    // This function is used to hide the Attribute buttons by default and shows the when on click
    function toggleOptions() 
    {
        var options = document.getElementById('options');
        if (options.style.display === 'none') {
            options.style.display = 'block';
        } else {
            options.style.display = 'none';
        }
    }

    // Redirect to the selected page when a checkbox is checked
    var checkboxes = document.querySelectorAll('#options input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) 
    {
        checkboxes[i].addEventListener('change', function() {
            if (this.checked) {
            window.location.href = this.value; // This redirect the users to the pages regarding to the attribute they select 
            }
        });
    }
</script>

<br>

<!-- Trending here -->
<div class="heading"> 
    <h4 class="history"> Trending </h4>
</div>   

<div class="container">
    <?php 
        // Trending SQL selects 10 items from table 'product' which match: Highest number of purchases and by recent 3 days
        $sql_trending = "SELECT * FROM product
        INNER JOIN ownedProd ON product.prod_id = ownedProd.prod_id
        WHERE ownedProd.date_buy >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        GROUP BY product.prod_id 
        ORDER BY COUNT(ownedProd.prod_id) DESC LIMIT 10
        ";
        
        if ($query_trending = mysqli_query($conn, $sql_trending)) {
            while ($row = mysqli_fetch_array($query_trending)) {
            ?>
                <div class="column">
                <a href="../child_page/detail.php?id=<?php echo $row["prod_id"] ?>" class="buying">
                    <img src="../../image/<?php echo $row["image"] ?>" class="cover">
                    <p class="prodname"> <?php echo $row["prod_name"] ?> </p>
                    <p class="price"> <?php echo $row["price"] ?> <br> Segg Coin </p>
                </a>
                </div>
            <?php
            }
        }
    ?>
</div>

<div class="clearfix"> </div>

<div class="heading"> 
    <h4 class="history"> Most Purchased </h4>
</div> 

<div class="container">
    <?php
    // SQL most buy selects 10 items which match: Highest number of purchases of all time
        $sql_most_buy = "SELECT * FROM product
        INNER JOIN ownedProd ON product.prod_id = ownedProd.prod_id
        GROUP BY product.prod_id
        ORDER BY COUNT(ownedProd.prod_id) DESC LIMIT 10
        ";

        if ($query_most_buy = mysqli_query($conn, $sql_most_buy)) {
            while ($row = mysqli_fetch_array($query_most_buy)) {
            ?>
            <div class="column">
            <a href="../child_page/detail.php?id=<?php echo $row["prod_id"] ?>" class="buying">
                <img src="../../image/<?php echo $row["image"] ?>" class="cover">
                <p class="prodname"> <?php echo $row["prod_name"] ?> </p>
                <p class="price"> <?php echo $row["price"] ?> <br> Segg Coin </p>
            </a>
            </div>
            <?php
            }
        }
    ?>
</div>

<div class="clearfix"> </div>

<!-- Recently here -->
<div class="heading"> 
    <h4 class="history"> Recently Uploaded </h4>
</div>

<div class="container">
    <?php 
    // Sql recent selects 10 items which match: Latest uploaded or sold 
        $sql_recent = "SELECT * FROM dataProd 
        INNER JOIN product ON dataProd.prod_id = product.prod_id
        ORDER BY product.date_created DESC LIMIT 10";
        $query_recent = mysqli_query($conn, $sql_recent);
        if ($query_recent) {
            while ($row = mysqli_fetch_array($query_recent)) {
            ?>
            <div class="column">
            <!-- This link redirects to the detail page of the appropriate item based on its id -->
            <a href="../child_page/detail.php?id=<?php echo $row["prod_id"] ?>" class="buying"> 
                <img src="../../image/<?php echo $row["image"] ?>" class="cover">
                <p class="prodname"> <?php echo $row["prod_name"] ?> </p>
                <p class="price"> <?php echo $row["price"] ?> <br> Segg Coin </p>
            </a>
            </div>
            <?php
            }
        } 
    ?>
   
</div>

<div class="clearfix"> </div>
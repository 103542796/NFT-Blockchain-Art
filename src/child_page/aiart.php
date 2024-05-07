<?php
include("../all_page/header.php");
?>

<link href="../../style/childpage.css" rel="stylesheet">
<link href="../../style/filterpage.css" rel="stylesheet">
<?php include("../all_page/body.php") ?>
<?php 
    require_once("../user_info/settings.php");
    include("../functions/functions.php");
?>

<div class="main"> 
<a href="../main/discoverpage.php" class="goingback"> <i class="bi bi-arrow-90deg-left"></i> Back </a>

    <div class="heading"> 
        <h4 class="history"> AI Generated Art </h4>
    </div>

    <div class="container">
        <?php 
            $artworksPerPage = 8; // Number of artwork per page 
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
            $offset = ($currentPage - 1) * $artworksPerPage; // Calculate the offset for the SQL query
            $sql_recent = "SELECT * FROM product WHERE product.attribute LIKE '%AI Art%'
            ORDER BY product.date_created DESC 
            LIMIT  $offset, $artworksPerPage";
            
            $query_recent = mysqli_query($conn, $sql_recent);
            if ($query_recent) {
                while ($row = mysqli_fetch_array($query_recent)) {
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
        
        <!-- Pagination links --> 
        <div class="pagination">
        <?php
            $sql_count = "SELECT COUNT(*) FROM product
            WHERE product.attribute LIKE '%AI Art%'";

            $query_count = mysqli_query($conn, $sql_count);
            $totalItems = mysqli_fetch_row($query_count)[0];
            $total_pages = ceil($totalItems / $artworksPerPage);

            if ($currentPage > 1) {
                echo '<a href="aiart.php?page=' . ($currentPage - 1) . '">Previous</a>';
            } 
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a ' . ($i == $currentPage ? 'class="active"' : '') . ' href="?page=' . $i . '">' . $i . '</a>';
            }
            if ($currentPage < $total_pages) {
                echo '<a href="aiart.php?page=' . ($currentPage + 1) . '">Next</a>';
            }
        ?>
        </div>

        <div class="clearfix"></div>

    </div>


</div>
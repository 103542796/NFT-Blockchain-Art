<?php
include("../all_page/header.php");
?>

<link href="../../style/childpage.css" rel="stylesheet">
<link href="../../style/mycollection.css" rel="stylesheet">
<?php include("../all_page/body.php"); ?>
<?php 
    require_once("../user_info/settings.php");
    session_start();

    if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) // Validate if login is successful or not
    {
        header("location: ../user_info/login.php"); // If not, redirect to the login page.
        exit();
    }
?>

<div class="main">  
    <div class="heading"> 
        <h4 class="history"> My Collection </h4>
    </div>

    <?php 
    $artworksPerPage = 8; // Number of artwork per page 
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
    $offset = ($currentPage - 1) * $artworksPerPage; // Calculate the offset for the SQL query

    // This sql selects all products in which belong to user who is the one currently logging in. 
    // The ownership is chosen from the ownedProd table and limit 8 artwors per Page (below Pagination)
    $sql_collect = "SELECT * FROM product
    INNER JOIN ownedProd ON product.prod_id = ownedProd.prod_id
    WHERE ownedProd.user_id = '{$_SESSION['uid']}'
    ORDER BY product.date_created
    LIMIT $offset, $artworksPerPage";

    $query_collect = mysqli_query($conn, $sql_collect);

    if ($query_collect) {
        while ($row_collect = mysqli_fetch_array($query_collect)) {
            ?>
            <div class="container2">
                <div class="column2">
                <!-- This link redirects to the owned page of the appropriate item based on its id -->
                    <a href="../child_page/owned.php?id=<?php echo $row_collect["prod_id"] ?>" class="buying">
                        <img src="./../../image/<?php echo $row_collect["image"] ?>" class="cover">
                        <p class="prodname"> <?php echo $row_collect["prod_name"] ?> </p>
                        <p class="price"> <?php echo $row_collect["price"] ?> <br> Segg Coin </p>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    ?>  

    <div class="pagination">
        <?php
        // This sql counts the number of product based on the SQL above to decide pagination
        $sql_count = "SELECT COUNT(*) FROM product
        INNER JOIN ownedProd ON product.prod_id = ownedProd.prod_id
        WHERE ownedProd.user_id = '{$_SESSION['uid']}'";

        $query_count = mysqli_query($conn, $sql_count);
        $totalItems = mysqli_fetch_row($query_count)[0];
        $totalPages = ceil($totalItems / $artworksPerPage);

        if ($currentPage > 1) { 
            echo '<a href="mycollection.php?page=' . ($currentPage - 1) . '">Previous</a>';
        } 
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a ' . ($i == $currentPage ? 'class="active"' : '') . ' href="?page=' . $i . '">' . $i . '</a>';
        }
        if ($currentPage < $totalPages) {
            echo '<a href="mycollection.php?page=' . ($currentPage + 1) . '">Next</a>';
        }
        ?>
    </div>

    <div class="clearfix"> </div>
</div>

<?php include("../all_page/footer.php"); ?>

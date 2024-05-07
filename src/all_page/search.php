<?php include("header.php"); ?>
<link href="../../style/childpage.css" rel="stylesheet">
<!-- <link href="../../style/discoverpage.css" rel="stylesheet"> -->
<link href="../../style/search.css" rel="stylesheet">

<?php include("body.php"); ?>

<div class="main"> 
  <div class="container">
  <?php
    session_start();  
    require_once("../user_info/settings.php");

    $artworksPerPage = 8; // Number of artwork per page 
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
    $offset = ($currentPage - 1) * $artworksPerPage; // Calculate the offset for the SQL query

    $prod_name = "";

    if (isset($_POST["prod_name"])) {
      $prod_name = strtolower($_POST["prod_name"]); // This code takes the input into a variable and store as session
      $_SESSION["searchTerm"] = $prod_name;
    }

    // Check if there's no search term input
    if (empty($_SESSION["searchTerm"])) {
      echo "<p>Please enter the search bar.</p>"; // If no input
    } else {
      // This sql shows all the products which name contains the input in their names 
      // Limit 8 products shown in a page with pagination 
      $query = "SELECT * FROM product WHERE LOWER(prod_name) LIKE '%" . $_SESSION["searchTerm"] . "%' LIMIT $offset, $artworksPerPage";
      $results = mysqli_query($conn, $query);

      if ($results) {
        while ($row = mysqli_fetch_array($results)) {
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
          ?>
            <div class="pagination">
              <?php
              // The sql counts the number of records from the SQL above to decide pagination
                $sql_count = "SELECT COUNT(*) FROM product
                WHERE LOWER(prod_name) LIKE '%" . $_SESSION["searchTerm"] . "%' ";

                $query_count = mysqli_query($conn, $sql_count);
                $totalItems = mysqli_fetch_row($query_count)[0];
                $total_pages = ceil($totalItems / $artworksPerPage);

                if ($currentPage > 1) {
                  echo '<a href="search.php?page=' . ($currentPage - 1) . '&prod_name=' . urlencode($_SESSION["searchTerm"]) . '">Previous</a>';
                } 
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<a ' . ($i == $currentPage ? 'class="active"' : '') . ' href="?page=' . $i . '&prod_name=' . urlencode($_SESSION["searchTerm"]) . '">' . $i . '</a>';
                }
                if ($currentPage < $total_pages) {
                    echo '<a href="search.php?page=' . ($currentPage + 1) . '&prod_name=' . urlencode($_SESSION["searchTerm"]) . '">Next</a>';
                }
              
              ?>
            </div>
          <?php
      } 
      else
      {
        echo "<p style='color:red'> No data available </p>";
      }
    }
  ?>
  
  <div class="clearfix"></div>

  </div>
</div>

<?php include("footer.php"); ?>

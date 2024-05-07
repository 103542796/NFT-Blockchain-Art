<?php include("../all_page/header.php"); ?>
<link href="../../style/childpage.css" rel="stylesheet">
<link href="../../style/history.css" rel="stylesheet">

<?php include("../all_page/body.php"); ?>
<?php 
    require_once("../user_info/settings.php");
    session_start();

    if(!isset($_SESSION["login"]) || isset($_SESSION["login"]) !== true) // validate if login successful or not
    {
        header("location: ../user_info/login.php"); // if not header to index file so they must register or login first
        exit();
    }
?>
<div class="main"> 
    <h2> History Transaction </h2>
        <div class="tb-history"> 
        <table class="tb-detail">
            <thead>
            <tr>
                <th>Transaction No</th>
                <th> Date Created </th>
                <th> Action </th>
                <th> Label </th>
                <th> Amount </th>
            </tr>
            </thead>

        <?php
            require_once("../user_info/settings.php");
            $artworksPerPage = 10; // Number of artwork per page 
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
            $offset = ($currentPage - 1) * $artworksPerPage; // Calculate the offset for the SQL query
            
            $sql = "SELECT * FROM history
            INNER JOIN myHistory ON history.history_id = myHistory.history_id
            WHERE myHistory.user_id = '{$_SESSION['uid']}' 
            ORDER BY history.date_time DESC
            LIMIT $offset, $artworksPerPage";

            $res = mysqli_query($conn, $sql); //Make Query
            $count = mysqli_num_rows($res);     //Count
            $idcount = $offset + 1;                            // ID integrate

            if($count > 0)
            {
                while($row = mysqli_fetch_assoc($res))
                {
                    $time = $row['date_time'];
                    $action = $row['action'];
                    $label = $row['label'];
                    $amount = $row['amount'];
                    ?>
                    <tr> 
                        <td> <?php echo $idcount++; ?> . </td>
                        <td> <?php echo $time; ?> </td>
                        <td> <?php echo $action; ?> </td>
                        <td> <?php echo $label; ?> </td>
                        <td> <?php echo $amount; ?> </td>
                    </tr>

                    <?php 
                }
            }
            else
            {
                echo "<tr> <td colspan='12' class='error' > No Transactions </td></tr>";
            }
            ?>

        </table>
        <br>
        <div class="pagination">
            <?php
                $sql_count = "SELECT COUNT(*) FROM history
                INNER JOIN myHistory ON history.history_id = myHistory.history_id
                WHERE myHistory.user_id = '{$_SESSION['uid']}' ";

                $query_count = mysqli_query($conn, $sql_count);
                $totalItems = mysqli_fetch_row($query_count)[0];
                $totalPages = ceil($totalItems / $artworksPerPage);

                if ($currentPage > 1) {
                    echo '<a href="history.php?page=' . ($currentPage - 1) . '">Previous</a>';
                } 
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo '<a ' . ($i == $currentPage ? 'class="active"' : '') . ' href="?page=' . $i . '">' . $i . '</a>';
                }
                if ($currentPage < $totalPages) {
                    echo '<a href="history.php?page=' . ($currentPage + 1) . '">Next</a>';
                }
            ?>
        </div>

        </div>
</div>

<?php include("../all_page/footer.php"); ?>
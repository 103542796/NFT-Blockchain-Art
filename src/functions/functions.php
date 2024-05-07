<?php
function ShowErrorMsg($errMsg) // Shows error message 
{
    $countError = count($errMsg);
    if($countError >0 )
    {
        foreach($errMsg as $i)
        {
            echo "<p> $i </p>";
        }
    }
}

?>
<?php
    //Validation
    session_start();
    if(($_SESSION['admin'])==1){
    $error_fields = array();
    // Open the connection
    $conn = mysqli_connect("localhost", "root", "", "shop_db");
    if (! $conn) {
        echo mysqli_connect_error();
        exit;
    }
    // Select user
    $id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
    $query = "DELETE FROM `users` WHERE `id` = ".$id." LIMIT 1 ";
    if(mysqli_query($conn,$query)){
        header("location: list.php");
        exit; 
    }else{
        echo $query;
        echo mysqli_error($conn);
    }
    // Close the  connection
    mysqli_close($conn);
}else {
    echo "<span class='error'>* You don't have permission</span>";
    header("location: list.php");
}

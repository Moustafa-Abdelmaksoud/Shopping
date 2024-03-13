<?php
    session_start();

    //Validation
    $error_fields = array();
    // Open the connection
    $conn = mysqli_connect("localhost", "root", "", "shop_db");
    if (! $conn) {
        echo mysqli_connect_error();
        exit;
    }
    // Select the user
    // edit.php?id= => $_GET['id']
    $id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
    $select = "SELECT * FROM `users` WHERE `users`.`id` =".$id." LIMIT 1 ";
    $result = mysqli_query($conn,$select);
    $row = mysqli_fetch_assoc($result);

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        if(!(isset($_POST["name"]) && !empty($_POST["name"]))){
            $error_fields[] = "name";
        }
        if(!(isset($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))){
            $error_fields[] = "email";
        }
    
    if(!$error_fields){
        if(($_SESSION['admin'])==1){
        // Escape any sepcial characters to avoid SQL Injection
        $name = mysqli_escape_string($conn, trim($_POST['name']));
        $email = mysqli_escape_string($conn, $_POST['email']);
        $password =(!empty($_POST['password'])) ? password_hash($_POST['password'],PASSWORD_DEFAULT) : $row['password'];
        $admin = (isset($_POST['admin'])) ? 1 : 0 ;
        $query = "UPDATE `users` SET `name` = '".$name."',`email` = '".$email."',
        `password` = '".$password."', `admin` = " .$admin. " WHERE `users`.`id` =". $id;
        if(mysqli_query($conn, $query)){
            header("Location: list.php");
            exit;
        }else{
            echo mysqli_error($conn);
        }
        }
    }
    }
    mysqli_free_result($result);
    mysqli_close($conn);
?>

<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Admin :: Edit User</title>
</head>
    <body>
        <form method="post" enctype="multipart/form-data">
        <?php
            if ($_SESSION['admin'] == 0) {
                echo "<span class='error'>* You don't have permission</span>";
            } else {
                echo "<span class='hello'>Hello, admin</span>";
            }
        ?>
            <input type="hidden" name="id" id="id" value="<?= isset($row['id'])? $row['id'] : '' ?>">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?=(isset($row['name'])) ? $row['name'] : '' ?>" /><?php if(in_array("name", $error_fields)) 
            echo "<span class='error'>* Please enter your name</span>"; ?>
            <br />
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?=(isset($row['email'])) ? $row['email'] : '' ?>" /><?php if(in_array("email", $error_fields)) 
            echo "<span class='error'>* Please enter a valid email</span>"; ?>
            <br />
            <label for="password">Password</label>
            <input type="password" name="password" id="password" /><?php if(in_array("password", $error_fields)) 
            echo "<span class='error'>* Please enter a password not less than 6 characters</span>"; ?>
            <br />
            <input type="checkbox" name="admin" <?= (isset($row['admin'])) ? 'checked' : ''?> />Admin
            <br>
            <input type="submit" name="submit" value="Edit User" />
        </form> 
    </body>
</html>
    

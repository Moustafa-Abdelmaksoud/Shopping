<?php
    //Validation
    session_start();
    $error_fields = array();
    if(($_SERVER['REQUEST_METHOD'] == 'POST')){
        if(($_SESSION['admin'])==1)
        {
        if(!(isset($_POST["name"]) && !empty($_POST["name"]))){
            $error_fields[] = "name";
        }
        if(!(isset($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))){
            $error_fields[] = "email";
        }
        if(!(isset($_POST["password"]) && strlen($_POST["password"] > 5))){
            $error_fields[] = "password";
        }
    
    if(!$error_fields){
        // Open the connection
        $conn = mysqli_connect("localhost", "root", "", "shop_db");
        if (! $conn) {
            echo mysqli_connect_error();
            exit;
        }
        
        // Escape any sepcial characters to avoid SQL Injection
        $name = mysqli_escape_string($conn,trim($_POST['name']));
        $email = mysqli_escape_string($conn, $_POST['email']);
        $password = mysqli_escape_string($conn, $_POST['password']);
        $hashedpassword = password_hash($password,PASSWORD_DEFAULT);
        $admin = (isset($_POST['admin'])) ? 1 : 0 ;
        // Insert the data
        $select = mysqli_query($conn,"SELECT * FROM `users` WHERE `email` = '$email'") or die('query faild');
            if(mysqli_num_rows($select) > 0){
                if($row = mysqli_fetch_assoc($select)){
                    $hashedpassword = $row['password'];
                    if(password_verify($password,$hashedpassword)){
                        $message[] = 'User already exist';    
                        }else{
                            $message[] = "This email is already registered. Please use a different email.";
                    }
                }
                mysqli_free_result($select);
            }else{
                $query = "INSERT INTO `users` (`name`, `email`, `password`, `admin`) 
                VALUES ('".$name."','".$email."','".$hashedpassword."', '".$admin."')";// '".$name."' because it's a way to ensure that the variable is properly included in the string as a discrete element
                if (mysqli_query($conn,$query)) {
                    $message[] = 'Added successfully!';
                    header("location: list.php");
                }else{
                    echo $query;
                    echo mysqli_error($conn);
                }
            }
            mysqli_close($conn);
        }
      }
    }

?>

<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Admin :: Add User</title>
</head>
    <body>
        
        <form method="post" enctype="multipart/form-data">
        <?php
            if(isset($message)){
                foreach($message as $message){
                    echo '<div class="message" onclick="this.remove(">'.$message.'</div>';
                }
            } 
        ?>
        <?php
            if ($_SESSION['admin'] == 0) {
                echo "<span class='error'>* You don't have permission</span>";
            } else {
                echo "<span class='hello'>Hello, admin</span>";
            }
            ?>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?=(isset($_POST['name'])) ? $_POST['name'] : '' ?>" /><?php if(in_array("name", $error_fields)) 
            echo "<span class='error'> * Please enter your name</span>"; ?>
            <br />
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?=(isset($_POST['email'])) ? $_POST['email'] : '' ?>" /><?php if(in_array("email", $error_fields)) 
            echo "<span class='error'>* Please enter a valid email</span>"; ?>
            <br />
            <label for="password">Password</label>
            <input type="password" name="password" id="password" /><?php if(in_array("password", $error_fields)) 
            echo "<span class='error'>* Please enter a password not less than 6 characters</span>"; ?>
            <br />
            <input type="checkbox" name="admin" <?= (isset($_POST['admin'])) ? 'checked' : ''?> />Admin
            <br>
            <input type="submit" name="submit" value="Add User" />
        </form> 
    </body>
</html>
    

<?php
    $error_fields = array();
    if(isset($_POST['submit'])){
        //Validation
        if (!isset($_POST["name"]) || empty($_POST["name"])) {
        $error_fields[] = "name";
        }
        if (!isset($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $error_fields[] = "email";
        }
        if (!isset($_POST["password"]) || strlen($_POST["password"]) <= 5) {
            $error_fields[] = "password";
        }
        if (!isset($_POST["cpassword"]) || $_POST["cpassword"] !== $_POST["password"]) {
            $error_fields[] = "cpassword";
        }
        if(!$error_fields){
            // Open the connection
            $conn = mysqli_connect("localhost", "root", "", "shop_db");
            if (! $conn) {
                    echo mysqli_connect_error();
                    exit;
            }
            // Escape any sepcial characters to avoid SQL Injection
            $name = mysqli_escape_string($conn, trim($_POST['name']));
            $email = mysqli_escape_string($conn, $_POST['email']);
            $password = mysqli_escape_string($conn, $_POST['password']);
            $hashedpassword = password_hash($password,PASSWORD_DEFAULT);

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
                $query = "INSERT INTO `users` (`name`, `email`, `password`) 
                VALUES ('".$name."','".$email."','".$hashedpassword."')";// '".$name."' because it's a way to ensure that the variable is properly included in the string as a discrete element
                if (mysqli_query($conn,$query)) {
                    $message[] = 'Registerd successfully!';
                    header("location: login.php");
                }else{
                    echo $query;
                    echo mysqli_error($conn);
                }
            }
            mysqli_close($conn);
        }
    
    }
    
    
    
?>   
    

<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Register</title>
</head>
<body>
    <?php
    if(isset($message)){
        foreach($message as $message){
            echo '<div class="message" onclick="this.remove(">'.$message.'</div>';
        }
    } 
    
    ?>
    <div class="form-container" >

        <form method="post" enctype="multipart/form-data">
            <h3>Register</h3>
            <input type="text" name="name" class="box" id="name"  placeholder="Enter username"><?php if(in_array("name", $error_fields)) 
            echo "<span class='error'>* Please enter your name</span>"; ?>
            <br>
            <input type="email" name="email" class="box" id="email"  placeholder="Enter email"><?php if(in_array("email", $error_fields)) 
            echo "<span class='error'>* Please enter a valid email</span>"; ?>
            <br>
            <input type="password" name="password" class="box" id="password"  placeholder="Enter password"><?php if(in_array("password", $error_fields)) 
            echo "<span class='error'>* Please enter a password not less than 6 characters</span>"; ?>
            <input type="password" name="cpassword" class="box" id="password"  placeholder="confirm password"><?php if(in_array("password", $error_fields))
            echo "<span class='error'>* Password not the same </span>"; ?>
            <br>
            <input type="submit" name="submit" class="btn" value="Register">
            <br>
            <b>have an account?<a href="login.php">LOGIN</a></b>
        </form>
    </div>
</body>
</html>

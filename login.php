<?php
session_start();
$error_fields = array();
if (isset($_POST['submit'])) {
    //Validation
    if (!(isset($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
        $error_fields[] = "email";
    }
    if (!(isset($_POST["password"]) && strlen($_POST["password"] > 5))) {
        $error_fields[] = "password";
    }
    if (!$error_fields) {
        // Open the connection
        $conn = mysqli_connect("localhost", "root", "", "shop_db");
        if (!$conn) {
            echo mysqli_connect_error();
            exit;
        }
        // Escape any sepcial characters to avoid SQL Injection
        $email = mysqli_escape_string($conn, $_POST['email']);
        $password = mysqli_escape_string($conn, $_POST['password']);
        $select = mysqli_query($conn, "SELECT * FROM `users` WHERE `email` = '$email'") or die('query faild');
        if (mysqli_num_rows($select) > 0) {
            if ($row = mysqli_fetch_assoc($select)) {
                $hashedpassword = $row['password'];
                if (!password_verify($password, $hashedpassword)) {
                    $message[] = 'Invalid email or password';
                } else {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['admin'] = $row['admin'];
                    header("location: index.php");
                }
            }

            mysqli_free_result($select);
        } else {
            $message[] = 'Invalid email or password';
        }
        mysqli_close($conn);
    }
}



?>


<html>

<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Login</title>
</head>

<body>
    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '<div class="message" onclick="this.remove(">' . $message . '</div>';
        }
    }

    ?>
    <div class="form-container">

        <form method="post" enctype="multipart/form-data">
            <h3>LOGIN</h3>
            <input type="email" name="email" class="box" id="email" placeholder="Enter email"><?php if (in_array("email", $error_fields))
                                                                                                    echo "<span class='error'>* Please enter a valid email</span>"; ?>
            <br>
            <input type="password" name="password" class="box" id="password" placeholder="Enter password"><?php if (in_array("password", $error_fields))
                                                                                                                echo "<span class='error'>* Please enter a password not less than 6 characters</span>"; ?>
            <br>
            <input type="submit" name="submit" class="btn" value="Register">
            <br>
            <b>don't have an account?<a href="register.php">REGISTER</a></b>
        </form>
    </div>
</body>

</html>

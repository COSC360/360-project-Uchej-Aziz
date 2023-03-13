<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register an account</title>

    <style>

    </style>
</head>

<body>
    <form action="register.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username"><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password"><br>

        <label for="repassword">Confirm Password:</label>
        <input type="password" name="repassword" id="repassword"><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email"><br>

        <input type="submit" name="submit" value="Register 😎">
        <p><a href="login.php">Or Login</a></p>
    </form>

</body>

</html>

<?php
require("connect.php");
// echo $connector;

// $connect = mysqli_connect("127.0.0.1", "root", '', "php_forum");

$username = @$_POST['username'];
$password = @$_POST['password'];
$repass = @$_POST['repassword'];
$email = @$_POST['email'];
$date = date("Y-m-d");

$pass_en = sha1($password);

// echo $date;

if (!$connect) {
    die("Couldn't connect: " . mysqli_connect_error());
} else {
    echo "<br>Yaaay!<br>";
}

if (!$selectDb) {
    die("Error selecting database: " . mysqli_error($connect));
} else {
    echo "<br>Selected Database!<br>";
}



if (isset($_POST['submit'])) {

    if ($username && $password && $repass && $email) {

        if (strlen($username) >= 5 && strlen($username) <= 25 && strlen($password) > 6) {

            if ($repass == $password) {

                echo "<br>Success. Passwords match<br>";
                $query = mysqli_query($connect, "INSERT INTO users (`id`, `username`, `password`, `email`, `date`) VALUES ('', '" . $username . "', '" . $pass_en . "', '" . $email . "', '" . $date . "')");

                if ($query) {
                    echo "<br>You have been registered as $username. Click <a href='login.php'>here</a> to login<br>";
                } else {
                    echo "<br>Failure on query<br>";
                }
            } else {
                echo "<br>Passwords don't match!<br>";
            }
        } else {
            if (strlen($username) < 5 || strlen($username) > 25) {
                echo "<br>Username must be from 5 to 25 characters<br>";
            }

            if (strlen($password) < 6) {
                echo "<br>Password must be longer than 6 characters.<br>";
            }
        }
    } else {
        echo "<br>Please fill in all the fields.<br>";
    }


    // echo 'Username - ' . $username;
    // echo '<br>Password- ' . $password;
    // echo '<br>Repassword - ' . $repass;
    // echo '<br>Email - ' . $email;
}

// $query = mysqli_query($connect, "INSERT INTO users (`id`, `username`, `password`, `email`, `date`) VALUES ('', '" . $username . "', '" . $password . "', '" . $email . "', '')");
// if ($query) {
//     echo "Success";
// } else {
//     echo "Failure";
// }

// $table_1 = "users";
// $col_1_1 = "id";
// $col_1_2 = "username";
// $col_1_3 = "password";
// $col_1_4 = "email";


// $query = "INSERT INTO $table_1 ($col_1_1, $col_1_2, $col_1_3, $col_1_4) VALUES ('', '$username', '$password', '$email')";
// $result = mysqli_query($connect, $query);

// if ($result) {
//     echo "Query executed successfully";
// } else {
//     echo "Error executing query: " . mysqli_error($connect);
// }
?>
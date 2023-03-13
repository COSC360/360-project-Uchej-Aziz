<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with your account</title>
</head>

<body>
    <form action="login.php" method="POST">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username"><br>

        <label for="password">Password: </label>
        <input type="password" name="password" id="password"><br>

        <input type="submit" name="submit" value="Login 😎">
    </form>
</body>

</html>
<?php
session_start();

require("connect.php");

$username = @$_POST['username'];
$password = @$_POST['password'];
echo "yay";

if (isset($_POST['submit'])) {
    if ($username && $password) {
        $check = mysqli_query($connect, "SELECT * FROM users WHERE username='" . $username . "'");
        $rows = mysqli_num_rows($check);
        // echo $rows;

        if (mysqli_num_rows($check) != 0) {
            while ($row = mysqli_fetch_assoc($check)) {
                $db_username = $row["username"];
                $db_password = $row["password"];
            }
            if ($username == $db_username && sha1($password) == $db_password) {
                @$_SESSION["username"] = $username;
                header("Location: index.php");
            } else {
                echo "Your Password is wrong";
            }
        } else {
            die("Couldn't find user account");
        }
    } else {
        echo "Please fill in all the details";
    }
}
?>
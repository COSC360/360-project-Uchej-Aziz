<?php
session_start();
require("connect.php");

if (@$_SESSION["username"]) {
    // echo "Welcome " . $_SESSION["username"];

?>

    <html>
    <!-- <p>Logged in</p> -->

    <head>
        <title>Home Page</title>
    </head>
    <?php
    include "header.php";
    ?>

    <body>

    </body>

    </html>

<?php
    if (@$_GET["action"] == "logout") {
        session_destroy();
        header("Location: login.php");
    }
} else {
    echo "You must be logged in.";
}
?>
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
    echo "<center>: <h1>Members</h1>";
    $check = mysqli_query($connect, "SELECT * FROM users");
    $rows = mysqli_num_rows($check);

    while ($row = mysqli_fetch_assoc($check)) {
        $id = $row["id"];
        echo "<a href='profile.php?id=$id'>" . $row['username'] . "<br/></a>";
    }
    echo "</center>";
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
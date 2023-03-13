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

    echo "<center>";

    if (@$_GET['id']) {
        $check = mysqli_query($connect, "SELECT * FROM users WHERE id='" . $_GET['id'] . "'");
        $rows = mysqli_num_rows($check);

        if (mysqli_num_rows($check) != 0) {
            // echo "Success";
            while ($row = mysqli_fetch_assoc($check)) {
                echo "<h1>" . $row['username'] . "</h1><img src='" . $row['profile_pic'] . "' width='50' height='50'/><br/>";
                echo "<b>Date registered:</b> " . $row['date'] . "<br/>";
                echo "<b>Email:</b> " . $row['email'] . "<br/>";
                echo "<b>Replies:</b> " . $row['replies'] . "<br/>";
                echo "<b>Topics created:</b> " . $row['topics'] . "<br/>";
                echo "<b>Score:</b> " . $row['score'] . "<br/>";
            }
        } else {
            echo "ID not found.";
        }
    } else {
        header("Location: index.php");
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
<!DOCTYPE html>
<!-- <html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Boy</h1>
</body>

</html> -->

<?php

$connect = mysqli_connect("127.0.0.1", "root", '', "php_forum");
$dbName = "php_forum";

$selectDb = mysqli_select_db($connect, $dbName) or die("Couldn't connect to database");

// $connect = mysqli_connect("localhost", "igbobros", '1J2o3r4d5a6n123', "php_forum");
// $connector = 'JordanYo';
// echo "Jordan";
// echo '<br>Baba<br>';

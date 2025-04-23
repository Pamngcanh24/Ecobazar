<?php
function connect_db()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "laravel1";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
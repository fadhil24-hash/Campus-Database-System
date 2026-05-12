<?php
$conn = mysqli_connect("localhost", "root", "", "campus_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
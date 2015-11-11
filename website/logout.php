<?php
session_start();
include_once "include/conn.php";
include_once "include/functions.php";
include_once "include/header.php";
include_once "include/footer.php";

logout();
$conn->close();
header("Location: index.php");
?>
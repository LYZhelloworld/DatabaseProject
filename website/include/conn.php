<?php
$conn = new mysqli("localhost", "root", "root", "project");
if($conn -> connect_error) {
	die($conn -> connect_error);
}
?>
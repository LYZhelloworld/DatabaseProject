<?php
session_start();
include_once "include/conn.php";
include_once "include/functions.php";
include_once "include/header.php";
include_once "include/footer.php";

page_header("Index");
?>
<?php
if(checkAuth()):
?>
<p>Hello, <?php
$stmt = $conn->prepare("SELECT `name` FROM `customers` WHERE `loginname`=?;");
$stmt->bind_param("s", $_SESSION["login"]);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();
echo $name;
?>.</p>
<p><a href="books/">Books</a> | <a href="user/">User</a> | <a href="logout.php">Logout</a></p>
<?php
else:
?>
<p>Hello guest.</p>
<p><a href="reg.php">Register</a> | <a href="login.php">Login</a> | <a href="admin/login.php">Admin Login</a></p>
<?php
endif;
?>
<?php
page_footer();

$conn->close();
?>
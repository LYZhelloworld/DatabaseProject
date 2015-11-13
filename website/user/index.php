<?php
session_start();
include_once "../include/conn.php";
include_once "../include/functions.php";
include_once "../include/header.php";
include_once "../include/footer.php";

if(!checkAuth()) {
	$conn->close();
	header("Location: ../index.php");
	die();
}

$stmt = $conn->prepare("SELECT `name`, `credit`, `address`, `phone` FROM `customers` WHERE `loginname`=?;");
$stmt->bind_param("s", $_SESSION["login"]);
$stmt->execute();
$stmt->bind_result($name, $credit, $address, $phone);
$stmt->fetch();
$stmt->close();
$conn->close();

page_header("Account");
?>
<p><a href="index.php">Account</a> | <a href="history.php">Order history</a> | <a href="feedbacks.php">Feedbacks</a> | <a href="ratings.php">Ratings</a> | <a href="../index.php">Back</a></p>
<hr/>
<table>
<tbody>
<tr><td>Name</td><td>Credit</td><td>Address</td><td>Phone</td></tr>
<tr><td><?php echo $name; ?></td><td><?php echo $credit; ?></td><td><?php echo $address; ?></td><td><?php echo $phone; ?></td></tr>
</tbody>
</table>
<?php
page_footer();
?>
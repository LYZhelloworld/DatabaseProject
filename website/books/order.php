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

page_header("Order");
?>
<p><a href="index.php">Books</a> | <a href="search.php">Search</a> | <a href="../index.php">Back</a></p>
<hr/>
<?php
if(getarg("purchase") == null) {
	if(!isset($_SESSION["cart"])) {
		$_SESSION["cart"] = array();
	}
	if(count($_SESSION["cart"]) == 0) {
?>
<p>You have not purchased any books.</p>
<?php
	} else {
		$stmt = $conn->prepare("INSERT INTO `Orders` (`user`, `order_status`) VALUES (?,'Pending');");
		$stmt->bind_param("s", $_SESSION["login"]);
		$stmt->execute();
		$orderid = $conn->insert_id;
		$stmt->close();
		foreach($_SESSION["cart"] as $book=>$copies) {
			$stmt = $conn->prepare("INSERT INTO `Orderbooks` (`orderID`, `book`, `copies`);");
			$stmt->bind_param("isi", $orderid, $book, $copies);
			$stmt->execute();
			$stmt->close();
		}
?>
<p>Order has been submitted. Please wait until order is accepted by administrator.</p>
<?php
	}
} else {
	if(!isset($_SESSION["cart"])) {
		$_SESSION["cart"] = array();
	}
	if(!isset($_SESSION["cart"][getarg("purchase")])) {
		$_SESSION["cart"][getarg("purchase")] = 0;
	}
	$_SESSION["cart"][getarg("purchase")]++;
?>
<p>Book has been added to cart. <a href="#" onclick="window.history.back();return false;">Back</a></p>
<?php
}
$conn->close();
page_footer();
?>
<?php
session_start();
include_once "../include/conn.php";
include_once "../include/functions.php";
include_once "../include/header.php";
include_once "../include/footer.php";

if(!checkAdminAuth()) {
	$conn->close();
	header("Location: ../index.php");
	die();
}

page_header("Pending list");
?>
<p><a href="index.php">Statistics</a> | <a href="new.php">Add new book</a> | <a href="addcopies.php">Arrival of more copies</a> | <a href="pending.php">Orders in pending list</a> | <a href="../logout.php">Logout</a></p>
<hr/>
<?php
if(getarg("orderid") == null) {
	$stmt = $conn->prepare("SELECT `orderID` FROM `Orders` WHERE `order_status`='Pending';");
	$stmt->execute();
	$stmt->bind_result($orderid);
	while($stmt->fetch()) {
?>
<p>Order <?php echo $orderid; ?> <form action="pending.php" method="post"><input type="hidden" value="<?php echo $orderid; ?>" name="orderid" /><input type="submit" value="Approve" /></form></p>
<?php
		$stmt2 = $conn->prepare("SELECT `Books`.`title`, `Books`.`ISBN`, `Orderbooks`.`copies`, `Books`.`copies` FROM `Books`, `Orderbooks` WHERE `Orderbooks`.`orderID`=? AND `Books`.`ISBN`=`Orderbooks`.`book`;");
		$stmt2->bind_param("i", $orderid);
		$stmt2->execute();
		$stmt2->bind_result($book_title, $book_isbn, $order_copies, $book_copies);
		while($stmt2->fetch()) {
?>
<p><?php echo $book_isbn; ?> <em><?php echo $book_title; ?></em> (<?php echo $order_copies; ?> of <?php echo $book_copies; ?>)</p>
<?php
		}
		$stmt2->close();
	}
	$stmt->close();
} else {
	$error_occurred = FALSE;
	$no_enough_books = FALSE;
	$stmt = $conn->prepare("START TRANSACTION; SET autocommit = 0;");
	if(!$stmt->execute()) $error_occurred = TRUE;
	$stmt->close();
	$stmt = $conn->prepare("SELECT `Books`.`ISBN`, `Orderbooks`.`copies`, `Books`.`copies` FROM `Books`, `Orderbooks` WHERE `Orderbooks`.`orderID`=? AND `Books`.`ISBN`=`Orderbooks`.`book`;");
	$stmt->bind_param("i", getarg("orderid"));
	if(!$stmt->execute()) $error_occurred = TRUE;
	$stmt->bind_result($book_isbn, $order_copies, $book_copies);
	while($stmt->fetch()) {
		if($order_copies > $book_copies) {
			$no_enough_books = TRUE;
			break;
		}
		$result = $book_copies - $order_copies;
		$stmt2 = $conn->prepare("UPDATE `Books` SET `copies`=? WHERE `ISBN`=?;");
		$stmt2->bind_param("is", $result, $book_isbn);
		if(!$stmt2->execute()) $error_occurred = TRUE;
		$stmt2->close();
	}
	$stmt->close();
	$stmt = $conn->prepare("UPDATE `Orders` SET `order_status`='Approved' WHERE `orderID`=?;");
	$stmt->bind_param("i", getarg("orderid"));
	if(!$stmt->execute()) $error_occurred = TRUE;
	$stmt->close();
	
	if(!$error_occurred and !$no_enough_books) {
		$stmt = $conn->prepare("COMMIT;");
		$stmt->execute();
		$stmt->close();
?>
<p>Order <?php echo getarg("orderid"); ?> has been approved. <a href="#" onclick="window.history.back();return false;">Back</a></p>
<?php
	} elseif($no_enough_books) {
		$stmt = $conn->prepare("ROLLBACK;");
		$stmt->execute();
		$stmt->close();
?>
<p>No enough books for this order. <a href="#" onclick="window.history.back();return false;">Back</a></p>
<?php
	} else {
		$stmt = $conn->prepare("ROLLBACK;");
		$stmt->execute();
		$stmt->close();
	}
?>
<p>An error occurred during the transaction. <a href="#" onclick="window.history.back();return false;">Back</a></p>
<?php
}
?>
<?php
$conn->close();
page_footer();
?>
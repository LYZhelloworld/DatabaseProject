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

if(getarg("id") == null):
	$stmt = $conn->prepare("SELECT `orderID`, `order_date`, `order_status` FROM `Orders` WHERE `user`=?;");
	$stmt->bind_param("s", $_SESSION["login"]);
	$stmt->execute();
	$stmt->bind_result($orderID, $order_date, $order_status);

	page_header("Order History");
?>
<p><a href="index.php">Account</a> | <a href="history.php">Order history</a> | <a href="feedbacks.php">Feedbacks</a> | <a href="ratings.php">Ratings</a> | <a href="../index.php">Back</a></p>
<hr/>
<table>
<tbody>
<tr><td>Order No.</td><td>Date</td><td>Status</td></tr>
<?php
	while($stmt->fetch()) {
?>
<tr><td><a href="history.php?id=<?php echo $orderID; ?>"><?php echo $orderID; ?></a></td><td><?php echo $order_date; ?></td><td><?php echo $order_status; ?></td></tr>
<?php
	}
?>
</tbody>
</table>
<?php
	$stmt->close();
	$conn->close();
	page_footer();
else:
	$stmt = $conn->prepare("SELECT `Books`.`title`, `Orderbooks`.`book`, `Orderbooks`.`copies` FROM `Orderbooks`, `Books` WHERE `Books`.`ISBN`=`Orderbooks`.`book` AND `Orderbooks`.`orderID`=?;");
	$stmt->bind_param("i", getarg("id"));
	$stmt->execute();
	$stmt->bind_result($book_title, $book_isbn, $book_copies);
	
	page_header("Order History");
?>
<p><a href="index.php">Account</a> | <a href="history.php">Order history</a> | <a href="feedbacks.php">Feedbacks</a> | <a href="ratings.php">Ratings</a> | <a href="../index.php">Back</a></p>
<hr/>
<p>Order No: <?php echo getarg("id");?></p>
<table>
<tbody>
<tr><td>Book</td><td>ISBN</td><td>Copies</td></tr>
<?php
	while($stmt->fetch()) {
?>
<tr><td><?php echo $book_title; ?></td><td><?php echo $book_isbn; ?></td><td><?php echo $book_copies; ?></td></tr>
<?php
	}
?>
</tbody>
</table>
<?php
	$stmt->close();
	$conn->close();
	page_footer();
endif;
?>
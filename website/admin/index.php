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

page_header("Statistics");
?>
<p><a href="index.php">Statistics</a> | <a href="new.php">Add new book</a> | <a href="addcopies.php">Arrival of more copies</a> | <a href="pending.php">Orders in pending list</a> | <a href="../logout.php">Logout</a></p>
<hr/>
<p>Most popular books:</p>
<?php
$stmt = $conn->prepare("SELECT `Books`.`title`,`Books`.`ISBN` FROM `Books` WHERE `Books`.`ISBN` IN (SELECT `Orderbooks`.`book` FROM `Orderbooks`) ORDER BY (SELECT COUNT(*) FROM `Orderbooks` WHERE `Orderbooks`.`book`=`Books`.`ISBN`) DESC LIMIT 10;");
$stmt->execute();
$stmt->bind_result($book_title, $book_isbn);
while($stmt->fetch()) {
?>
<p><a href="index.php?isbn=<?php echo urlencode($book_isbn); ?>"><?php echo $book_title; ?></a></p>
<?php
}
$stmt->close();
?>
<p>Most popular authors:</p>
<?php
$stmt = $conn->prepare("SELECT `Books`.`authors` AS `authors1` FROM `Books` WHERE `Books`.`ISBN` IN (SELECT `Orderbooks`.`book` FROM `Orderbooks`) ORDER BY (SELECT COUNT(*) FROM `Orderbooks` WHERE `Orderbooks`.`book` IN (SELECT `Books`.`ISBN` FROM `Books` WHERE `Books`.`authors`=`authors1`)) DESC LIMIT 10;");
$stmt->execute();
$stmt->bind_result($book_authors);
while($stmt->fetch()) {
?>
<p><?php echo $book_authors; ?></p>
<?php
}
$stmt->close();
?>
<p>Most popular publishers:</p>
<?php
$stmt = $conn->prepare("SELECT `Books`.`publisher` AS `publisher1` FROM `Books` WHERE `Books`.`ISBN` IN (SELECT `Orderbooks`.`book` FROM `Orderbooks`) ORDER BY (SELECT COUNT(*) FROM `Orderbooks` WHERE `Orderbooks`.`book` IN (SELECT `Books`.`ISBN` FROM `Books` WHERE `Books`.`publisher`=`publisher1`)) DESC LIMIT 10;");
$stmt->execute();
$stmt->bind_result($book_authors);
while($stmt->fetch()) {
?>
<p><?php echo $book_authors; ?></p>
<?php
}
$stmt->close();

$conn->close();
page_footer();
?>
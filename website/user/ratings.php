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

$stmt = $conn->prepare("SELECT `Customers`.`name`, `Books`.`title`, `Rate`.`rating`, `Opinions`.`feedback` FROM `Customers`, `Books`, `Rate`, `Opinions` WHERE `Customers`.`loginname`=`Rate`.`user` AND `Books`.`ISBN`=`Rate`.`book` AND `Rate`.`rated_by`=? AND `Rate`.`user` = `Opinions`.`user` AND `Rate`.`book` = `Opinions`.`book`;");
$stmt->bind_param("s", $_SESSION["login"]);
$stmt->execute();
$stmt->bind_result($name, $title, $rating, $feedback);

page_header("Ratings");
?>
<p><a href="index.php">Account</a> | <a href="history.php">Order history</a> | <a href="feedbacks.php">Feedbacks</a> | <a href="ratings.php">Ratings</a> | <a href="../index.php">Back</a></p>
<hr/>

<?php
while($stmt->fetch()) {
?>
<p><?php echo $title; ?>, by <?php echo $name; ?> (<?php
switch ($rating) {
	case 0:
		echo "Useless";
		break;
	case 1:
		echo "Useful";
		break;
	case 2:
		echo "Very useful";
		break;
}
?>)</p>
<p><?php echo $feedback; ?></p>
<p></p>
<?php
}
?>

<?php
$stmt->close();
$conn->close();
page_footer();
?>
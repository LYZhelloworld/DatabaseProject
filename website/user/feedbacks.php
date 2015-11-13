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

$stmt = $conn->prepare("SELECT `Books`.`title`, `Opinions`.`score`, `Opinions`.`feedback`, `Opinions`.`feedback_date` FROM `Opinions`, `Books` WHERE `Opinions`.`user`=? AND `Books`.`ISBN`=`Opinions`.`book`;");
$stmt->bind_param("s", $_SESSION["login"]);
$stmt->execute();
$stmt->bind_result($title, $score, $feedback, $feedback_date);

page_header("Feedbacks");
?>
<p><a href="index.php">Account</a> | <a href="history.php">Order history</a> | <a href="feedbacks.php">Feedbacks</a> | <a href="ratings.php">Ratings</a> | <a href="../index.php">Back</a></p>
<hr/>

<?php
while($stmt->fetch()) {
?>
<p><?php echo $title; ?>, on <?php echo $feedback_date; ?></p>
<p>Score: <?php echo $score; ?> of 10</p>
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
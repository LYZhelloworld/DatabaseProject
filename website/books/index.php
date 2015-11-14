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

page_header("Books");
?>
<p><a href="index.php">Books</a> | <a href="search.php">Search</a> | <a href="../index.php">Back</a></p>
<hr/>
<?php
if(getarg("isbn") == null) {
?>
<p>Your cart:</p>
<?php
	if(!isset($_SESSION["cart"])) {
		$_SESSION["cart"] = array();
	}
	if(count($_SESSION["cart"]) == 0) {
?>
<p>Empty</p>
<?php
	} else {
		foreach($_SESSION["cart"] as $book=>$copies) {
			$stmt = $conn->prepare("SELECT `title` FROM `Books` WHERE `ISBN`=?;");
			$stmt->bind_param("s", $book);
			$stmt->execute();
			$stmt->bind_result($book_title);
			$stmt->fetch();
			$stmt->close();
			echo "<p>" . $book_title . " ($copies)</p>";
		}
?><p><a href="order.php">Order</a></p><?php
	}
} else {
	$stmt = $conn->prepare("SELECT `title`, `authors`, `publisher`, `year`, `copies`, `price`, `format`, `keywords`, `subject` FROM `Books` WHERE `ISBN`=?;");
	$stmt->bind_param("s", getarg("isbn"));
	$stmt->execute();
	$stmt->bind_result($title, $authors, $publisher, $year, $copies, $price, $format, $keywords, $subject);
	$stmt->fetch();
	$stmt->close();?>
<p><?php echo $title; ?> by <?php echo $authors; ?></p>
<p><?php echo $publisher; ?> (<?php echo $year; ?>)</p>
<p>ISBN: <?php echo getarg("isbn"); ?></p>
<p>Price: <?php echo $price; ?></p>
<p>Format: <?php echo $format; ?></p>
<p>Keywords: <?php echo $keywords; ?></p>
<p>Subject: <?php echo $subject; ?></p>
<p><?php echo $copies; ?> copies available now</p>
<p><a href="order.php?purchase=<?php echo urlencode(getarg("isbn")); ?>" >Purchase</a></p>
<hr/>
<p>Your feedback on this book</p>
<?php
	$stmt = $conn->prepare("SELECT count(*) FROM `Opinions` WHERE `user`=? AND `book`=?;");
	$stmt->bind_param("ss", $_SESSION["login"], getarg("isbn"));
	$stmt->execute();
	$stmt->bind_result($count_feedbacks);
	$stmt->fetch();
	$stmt->close();
	if($count_feedbacks == 0) {
?>
<form action="feedback.php" method="post">
<input type="hidden" value="<?php echo getarg("isbn"); ?>" name="book" />
<p>Score: <input type="number" min="1" max="10" name="score" required /> (1 for terrible, 10 for wonderful)</p>
<textarea row="4" col="50" maxlength="256" placeholder="Your feedback here..." name="feedback"></textarea>
<p><input type="submit" /><input type="reset" /></p>
</form>
<?php
	} else {
		$stmt = $conn->prepare("SELECT `score`, `feedback`, `feedback_date` FROM `Opinions` WHERE `user`=? AND `book`=?;");
		$stmt->bind_param("ss", $_SESSION["login"], getarg("isbn"));
		$stmt->execute();
		$stmt->bind_result($score, $feedback, $feedback_date);
		$stmt->fetch();
		$stmt->close();
?>
<p>Score: <?php echo $score; ?> (1 for terrible, 10 for wonderful)</p>
<p><?php if($feedback != null) echo $feedback; ?></p>
<p>Submitted on <?php echo $feedback_date; ?></p>
<?php
	}
?>
<hr/>
<p>Other people's feedback</p>
<?php
	$stmt = $conn->prepare("SELECT `Customers`.`name`, `Opinions`.`user`, `Opinions`.`score`, `Opinions`.`feedback`, `Opinions`.`feedback_date` FROM `Opinions` WHERE `Opinions`.`user`=`Customers`.`loginname` AND `Opinions`.`user`<>? AND `book`=? ORDER BY `Opinions`.`feedback_date` DESC LIMIT 10;");
	$stmt->bind_param("ss", $_SESSION["login"], getarg("isbn"));
	$stmt->execute();
	$stmt->bind_result($name, $loginname, $score, $feedback, $feedback_date);
	while($stmt->fetch()) {
		$stmt2 = $conn->prepare("SELECT `rating` FROM `Rate` WHERE `user`=? AND `book`=? AND rated_by=?");
		$stmt2->bind_param("sss", $loginname, getarg("isbn"), $_SESSION["login"]);
		$stmt2->execute();
		$stmt2->bind_result($rate_value);
		$rate_msg = null;
		if($stmt2->fetch()) {
			switch($rate_value) {
				case 0:
					$rate_msg = "useless";
					break;
				case 1:
					$rate_msg = "useful";
					break;
				case 2:
					$rate_msg = "very useful";
					break;
			}
?>
<p><?php echo $name; ?> on <?php echo $feedback_date; ?> (<?php echo $rate_msg; ?>)</p>
<p>Score: <?php echo $score; ?></p>
<p><?php echo $feedback; ?></p>
<?php
		} else {
?>
<p><?php echo $name; ?> on <?php echo $feedback_date; ?></p>
<p>Score: <?php echo $score; ?></p>
<p><?php echo $feedback; ?></p>
<form action="rate.php" method="post">
<input type="hidden" value="<?php echo $loginname; ?>" name="user" />
<input type="hidden" value="<?php echo getarg("isbn"); ?>" name="book" />
<p><input type="radio" value="0" name="rating" />Useless <input type="radio" value="1" name="rating" />Useful <input type="radio" value="2" name="rating" checked />Very useful</p>
<p><input type="submit" value="Rate" /></p>
</form>
<?php
		}
		$stmt2->close();
	}
	$stmt->close();
?>
<?php
}
?>
<?php
$conn->close();
page_footer();
?>
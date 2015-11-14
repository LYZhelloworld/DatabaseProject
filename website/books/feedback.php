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

page_header("Feedback");
?>
<p><a href="index.php">Books</a> | <a href="search.php">Search</a> | <a href="../index.php">Back</a></p>
<hr/>
<?php
if(getarg("book") == null or getarg("score") == null) {
?>
<p>Invalid number of arguments</p>
<?php
} else {
	$stmt = $conn->prepare("INSERT INTO `Opinions` (`user`, `book`, `score`, `feedback`) VALUES (?,?,?,?);");
	$stmt->bind_param("ssis", $_SESSION["login"], getarg("book"), getarg("score"), getarg("feedback"));
	$stmt->execute();
	$stmt->close();
?>
<p>Your feedback has been recorded. <a href="#" onclick="window.history.back();return false;">Back</a></p>
<?php
}
$conn->close();
page_footer();
?>
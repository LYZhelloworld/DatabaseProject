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

page_header("Rate");
?>
<p><a href="index.php">Books</a> | <a href="search.php">Search</a> | <a href="../index.php">Back</a></p>
<hr/>
<?php
if(getarg("book") == null or getarg("rating") == null or getarg("user") == null) {
?>
<p>Invalid number of arguments</p>
<?php
} else {
	$stmt = $conn->prepare("INSERT INTO `Rate` (`user`, `book`, `rating`, `rated_by`) VALUES (?,?,?,?);");
	$stmt->bind_param("ssis", getarg("user"), getarg("book"), getarg("rating"), $_SESSION["login"]);
	$stmt->execute();
	$stmt->close();
?>
<p>Your rating has been recorded. <a href="#" onclick="window.history.back();return false;">Back</a></p>
<?php
}
$conn->close();
page_footer();
?>
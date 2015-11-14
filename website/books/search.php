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

page_header("Search");
?>
<p><a href="index.php">Books</a> | <a href="search.php">Search</a> | <a href="../index.php">Back</a></p>
<hr/>
<?php
if(getarg("sort") == null or getarg("andor") == null) {
?>
<form action="search.php" method="get">
<p><input type="radio" name="andor" value="and" checked>And <input type="radio" name="andor" value="or">Or</p>
<p>Title: <input type="text" maxlength="256" name="title" /></p>
<p>Authors: <input type="text" maxlength="256" name="authors" /></p>
<p>Publisher: <input type="text" maxlength="256" name="publisher" /></p>
<p>Subject: <input type="text" maxlength="64" name="subject" /></p>
<p>Sorted by <input type="radio" name="sort" value="year" checked />year <input type="radio" name="sort" value="score" />score</p>
<p><input type="submit" /><input type="reset" /></p>
</form>
<?php
} else {
	if(getarg("andor") == "or") {
		$conj = "OR";
	} else {
		$conj = "AND";
	}
	if(getarg("sort") == "score") {
		$orderstring = "(SELECT AVG(`Opinions`.`score`) FROM `Opinions` WHERE `Opinions`.`book`=`Books`.`ISBN`) DESC;";
	} else {
		$orderstring = "`Books`.`year` DESC;";
	}
	$title_value = "%" . (getarg("title")==null?"":getarg("title")) . "%";
	$authors_value = "%" . (getarg("authors")==null?"":getarg("authors")) . "%";
	$publisher_value = "%" . (getarg("publisher")==null?"":getarg("publisher")) . "%";
	$subject_value = "%" . (getarg("subject")==null?"":getarg("subject")) . "%";
	$sql = "SELECT `Books`.`title`, `Books`.`ISBN` FROM `Books` WHERE `Books`.`title` LIKE ? " . $conj . "`Books`.`authors` LIKE ? " . $conj . "`Books`.`publisher` LIKE ? " . $conj . "`Books`.`subject` LIKE ? ORDER BY " . $orderstring;
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ssss", $title_value, $authors_value, $publisher_value, $subject_value);
	$stmt->execute();
	$stmt->bind_result($title, $isbn);
	$get_results = FALSE;
	while($stmt->fetch()) {
		$get_results = TRUE;
?>
<p><a href="index.php?isbn=" . <?php echo $isbn; ?> target="_blank"><?php echo $title; ?></a></p>
<?php
	}
	if(!$get_results) {
?>
<p>No results found.</p>
<?php
	}
	$stmt->close();
?>
<p><a href="search.php">Back</a></p>
<?php
}
?>
<?php
$conn->close();
page_footer();
?>
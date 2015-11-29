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

page_header("Add new book");
?>
<p><a href="index.php">Statistics</a> | <a href="new.php">Add new book</a> | <a href="addcopies.php">Arrival of more copies</a> | <a href="pending.php">Orders in pending list</a> | <a href="../logout.php">Logout</a></p>
<hr/>
<form action="new.php" method="post">
<p>ISBN: <input type="text" maxlength="14" name="isbn" required /></p>
<p>Title: <input type="text" maxlength="256" name="title" required /></p>
<p>Authors: <input type="text" maxlength="256" name="authors" /></p>
<p>Publisher: <input type="text" maxlength="256" name="publisher" /></p>
<p>Year: <input type="number" name="year" /></p>
<p>Price: <input type="number" name="price" step="0.01" min="0" value="0.00" /></p>
<p>Format: <select name="format"><option value="hardcover" selected>Hardcover</option><option value="softcover">Softcover</option></select></p>
<p>Keywords: <input type="text" name="keywords" maxlength="64" /></p>
<p>Subject: <input type="text" name="subject" maxlength="64" /></p>
<p>Copies: <input type="number" name="copies" min="0" value="0" /></p>
<p><input type="submit" /><input type="reset" /></p>
</form>
<?php
if(getarg("isbn") != null or getarg("title") != null or getarg("format") != null) {
	$stmt = $conn->prepare("INSERT INTO `Books` (`ISBN`, `title`, `authors`, `publisher`, `year`, `copies`, `price`, `format`, `keywords`, `subject`) VALUES (?,?,?,?,?,?,?,?,?,?);");
	$stmt->bind_param("ssssiidsss", getarg("isbn"), getarg("title"), getarg("authors"), getarg("publisher"), getarg("year"), getarg("copies"), getarg("price"), getarg("format"), getarg("keywords"), getarg("subject"));
	if($stmt->execute()) {
?>
<p>New book has been added successfully.</p>
<?php
	} else {
?>
<p>Failed to added records. ISBN may be duplicated or invalid arguments were provided.</p>
<?php
	}
	$stmt->close();
}
?>
<?php
$conn->close();
page_footer();
?>
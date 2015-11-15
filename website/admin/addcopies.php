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

page_header("Arrival of more copies");
?>
<p><a href="index.php">Statistics</a> | <a href="new.php">Add new book</a> | <a href="addcopies.php">Arrival of more copies</a> | <a href="pending.php">Orders in pending list</a> | <a href="../logout.php">Logout</a></p>
<hr/>
<?php
if(getarg("isbn") == null) {
?>
<form action="addcopies.php" method="post">
<p>ISBN <input type="text" maxlength="14" /><input type="submit" value="Search" /></p>
</form>
<?php
} elseif(getarg("copies") == null) {
	$stmt = $conn->prepare("SELECT `copies` FROM `Books` WHERE `ISBN`=?;");
	$stmt->bind_param("s", getarg("isbn"));
	$stmt->execute();
	$stmt->bind_result($copies);
	if($stmt->fetch()) {
?>
<form action="addcopies.php" method="post">
<p>ISBN <input type="text" maxlength="14" value="<?php echo getarg("isbn"); ?>" /><input type="submit" value="Search" /></p>
</form>
<form action="addcopies.php" method="post">
<input type="hidden" value="<?php echo getarg("isbn"); ?>" />
<p>Copies <input type="number" min="<?php echo $copies; ?>" name="copies" /><input type="submit" value="Add" /></p>
</form>
<?php
	} else {
?>
<form action="addcopies.php" method="post">
<p>ISBN <input type="text" maxlength="14" /><input type="submit" value="Search" /></p>
</form>
<p>Specific book does not exist.</p>
<?php
	}
	$stmt->close();
} else {
	$stmt = $conn->prepare("UPDATE `Books` SET `copies`=? WHERE `ISBN`=?;");
	$stmt->bind_param("is", getarg("isbn"), getarg("copies"));
	if($stmt->execute()) {
?>
<form action="addcopies.php" method="post">
<p>ISBN <input type="text" maxlength="14" /><input type="submit" value="Search" /></p>
</form>
<p>Record has been updated successfully.</p>
<?php
	} else {
?>
<form action="addcopies.php" method="post">
<p>ISBN <input type="text" maxlength="14" /><input type="submit" value="Search" /></p>
</form>
<p>Failed to update records. This may be because of invalid arguments passed or database error.</p>
<?php
	}
	$stmt->close();
}
?>
<?php
$conn->close();
page_footer();
?>
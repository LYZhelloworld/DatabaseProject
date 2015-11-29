<?php
session_start();
include_once "../include/conn.php";
include_once "../include/functions.php";
include_once "../include/header.php";
include_once "../include/footer.php";

if(getarg("usn") == null or getarg("pwd") == null):
	page_header("Admin Login");
?>
<form action="login.php" method="post">
<table>
<tbody>
<tr><td>Username</td><td><input type="text" name="usn" required maxlength="64" /></td></tr>
<tr><td>Password</td><td><input type="password" name="pwd" required maxlength="64" /></td></tr>
<tr><td colspan="2"><input type="submit" /><input type="reset" /></td></tr>
</tbody>
</table>
</form>
<?php
else:
	$stmt = $conn->prepare("SELECT COUNT(*) FROM `admin` WHERE `username`=? AND `password`=?;");
	$stmt->bind_param("ss", getarg("usn"), getarg("pwd"));
	$stmt->execute();
	$stmt->bind_result($lines);
	$stmt->fetch();
	$stmt->close();
	if($lines == 0):
		page_header("Login");
?>
<form action="login.php" method="post">
<table>
<tbody>
<tr><td>Username</td><td><input type="text" name="usn" required maxlength="64" /></td></tr>
<tr><td>Password</td><td><input type="password" name="pwd" required maxlength="64" /></td></tr>
<tr><td colspan="2"><input type="submit" /><input type="reset" /></td></tr>
</tbody>
</table>
</form>
<p>Invalid username or password.</p>
<?php
	else:
		adminAuth(getarg("usn"));
		$conn->close();
		header("Location: index.php");
		die();
	endif;
?>
<?php
endif;
?>
<p><a href="../index.php">Back</a></p>
<?php
page_footer();

$conn->close();
?>
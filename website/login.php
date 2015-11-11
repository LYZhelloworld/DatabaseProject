<?php
session_start();
include_once "include/conn.php";
include_once "include/functions.php";
include_once "include/header.php";
include_once "include/footer.php";

?>
<?php
if(getarg("usn") == null or getarg("pwd") == null):
	page_header("Login");
?>
<form action="login.php" method="post">
<table>
<tbody>
<tr><td>Username</td><td><input type="text" name="usn" /></td></tr>
<tr><td>Password</td><td><input type="password" name="pwd" /></td></tr>
<tr><td colspan="2"><input type="submit" /><input type="reset" /></td></tr>
</tbody>
</table>
</form>
<?php
else:
	$stmt = $conn->prepare("SELECT COUNT(*) FROM `customers` WHERE `loginname`=? AND `password`=?;");
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
<tr><td>Username</td><td><input type="text" name="usn" /></td></tr>
<tr><td>Password</td><td><input type="password" name="pwd" /></td></tr>
<tr><td colspan="2"><input type="submit" /><input type="reset" /></td></tr>
</tbody>
</table>
</form>
<p>Invalid username or password.</p>
<?php
	else:
		$stmt = $conn->prepare("SELECT `name` FROM `customers` WHERE `loginname`=?;");
		$stmt->bind_param("s", getarg("usn"));
		$stmt->execute();
		$stmt->bind_result($name);
		$stmt->fetch();
		auth($name);
		$conn->close();
		header("Location: index.php");
		die();
	endif;
?>
<?php
endif;
?>
<?php
page_footer();

$conn->close();
?>
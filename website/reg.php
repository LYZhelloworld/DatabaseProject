<?php
session_start();
include_once "include/conn.php";
include_once "include/functions.php";
include_once "include/header.php";
include_once "include/footer.php";

?>
<?php
if(getarg("usn") == null or getarg("pwd") == null or getarg("name") == null):
	page_header("Register");
?>
<form action="reg.php" method="post">
<table>
<tbody>
<tr><td>Name</td><td><input type="text" name="name" required maxlength="64" /></td></tr>
<tr><td>Login name</td><td><input type="text" name="usn" required maxlength="64" /></td></tr>
<tr><td>Password</td><td><input type="password" name="pwd" required maxlength="64" /></td></tr>
<tr><td>Credit</td><td><input type="number" name="credit" maxlength="16" /></td></tr>
<tr><td>Address</td><td><input type="text" name="address" maxlength="256" /></td></tr>
<tr><td>Phone</td><td><input type="text" name="phone" maxlength="32" /></td></tr>
<tr><td colspan="2"><input type="submit" /><input type="reset" /></td></tr>
</tbody>
</table>
</form>
<?php
else:
	$stmt = $conn->prepare("SELECT COUNT(*) FROM `customers` WHERE `loginname`=?;");
	$stmt->bind_param("s", getarg("usn"));
	$stmt->execute();
	$stmt->bind_result($lines);
	$stmt->fetch();
	$stmt->close();
	if($lines != 0):
		page_header("Register");
?>
<form action="reg.php" method="post">
<table>
<tbody>
<tr><td>Name</td><td><input type="text" name="name" required maxlength="64" /></td></tr>
<tr><td>Login name</td><td><input type="text" name="usn" required maxlength="64" /></td></tr>
<tr><td>Password</td><td><input type="password" name="pwd" required maxlength="64" /></td></tr>
<tr><td>Credit</td><td><input type="number" name="credit" maxlength="16" /></td></tr>
<tr><td>Address</td><td><input type="text" name="address" maxlength="256" /></td></tr>
<tr><td>Phone</td><td><input type="text" name="phone" maxlength="32" /></td></tr>
<tr><td colspan="2"><input type="submit" /><input type="reset" /></td></tr>
</tbody>
</table>
</form>
<p>Login name already exists.</p>
<?php
	else:
		$stmt = $conn->prepare("INSERT INTO `customers` (`name`, `loginname`, `password`, `credit`, `address`, `phone`) VALUES (?,?,?,?,?,?);");
		$stmt->bind_param("ssssss", getarg("name"), getarg("usn"), getarg("pwd"), getarg("credit"), getarg("address"), getarg("phone"));
		$stmt->execute();
		$stmt->close();
		auth($name);
		$conn->close();
		header("Location: login.php");
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
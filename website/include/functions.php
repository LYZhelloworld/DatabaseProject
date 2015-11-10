<?php
function checkAuth() {
	if(!isset($_SESSION["login"]))
		return FALSE;
	if($_SESSION["login"] != TRUE)
		return FALSE;
	return TRUE;
}

function checkAdminAuth() {
	if(!checkAuth())
		return FALSE;
	if(!isset($_SESSION["admin"]))
		return FALSE;
	if($_SESSION["admin"] != TRUE)
		return FALSE;
	return TRUE;
}
?>
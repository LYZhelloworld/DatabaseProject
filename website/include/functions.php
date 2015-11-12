<?php
function checkAuth() {
	if(!isset($_SESSION["login"]))
		return FALSE;
	if($_SESSION["login"] == "")
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

function auth($usn) {
	$_SESSION["login"] = $usn;
}

function adminAuth($usn) {
	$_SESSION["login"] = $usn;
	$_SESSION["admin"] = TRUE;
}

function logout() {
	unset($_SESSION["login"]);
	unset($_SESSION["admin"]);
}

function getarg($argname) {
	if(!isset($_REQUEST[$argname])) {
		return null;
	} elseif($_REQUEST[$argname] == "") {
		return null;
	} else {
		return $_REQUEST[$argname];
	}
}
?>
<?php
session_start();
include_once "include/conn.php";
include_once "include/functions.php";
include_once "include/header.php";
include_once "include/footer.php";

page_header("test");
?>
<p>Something goes here.</p>
<?php
page_footer();

$conn->close();
?>
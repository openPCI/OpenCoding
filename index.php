<?php
session_start();
if($_POST["logout"]) {
	session_unset();
	header("location:?p=main");
	exit;
}
$user_id=$_SESSION["user_id"];
$relative="./";
include_once("dirs.php");
// include_once($secretdir."settings.php");
include_once("header.php");
include_once($shareddir."database.php");
include_once($shareddir."templates.php");
?>
<div class="container-fluid" id="contentdiv">
<?php
	$p=($_POST["p"]?$_POST["p"]:($_GET["p"]?$_GET["p"]:"main"));
?>
</div>
<?php
//   print_r(ini_get_all());
include_once("footer.php");

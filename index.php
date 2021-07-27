<?php
session_start();
if($_POST["logout"]) {
	unset($_SESSION["user_id"]);
	unset($_SESSION["perms"]);
	unset($_SESSION["response_id"]);
	unset($_SESSION["flag_id"]);
	unset($_SESSION["training"]);
	unset($_SESSION["coder_id"]);
	unset($_SESSION["isdoublecode"]);
	unset($_SESSION["doublecodingpct"]);
	unset($_SESSION["codingadmin"]);
	unset($_SESSION["activetask"]);
	unset($_SESSION["difficulty"]);
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
	if($_GET["contact"]) {
		echo get_template("contact")["template"];
	}
	if($user_id) {
		$p=($_POST["p"]?$_POST["p"]:($_GET["p"]?$_GET["p"]:"main"));
		#echo get_template($p)["template"];
	}
?>
</div>
<?php
include_once("footer.php");

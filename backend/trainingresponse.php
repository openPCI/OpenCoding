<?php
session_start();
#checkpermissions()
// checkperm();
// else $user_id=$_SESSION["user_id"];
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("scoringadmin");

if($_SESSION["response_id"]!=$_POST["response_id"]) $warning=_("Response-id doesn't match.");
else {
	if($_POST["status"]=="istrainingresponse") $q='insert into trainingresponses (response_id,difficulty,manager_id) VALUE ('.$_POST["response_id"].','.$_POST["difficulty"].','.$_SESSION["user_id"].')';
	else $q='delete from trainingresponses where response_id='.$_POST["response_id"];
	$mysqli->query($q);
}
$log.="\n".$q;
$res["log"]=$log;
$res["warning"]=$warning;
echo json_encode($res);

<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");


if($_POST["user_id"]) {
	$q='delete from users where user_id='.$_POST["user_id"];
	$mysqli->query($q);
}$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);

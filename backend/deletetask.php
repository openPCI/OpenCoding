<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");


if($_POST["task_id"]) {
	$q='delete from tasks where task_id='.$_POST["task_id"];
	$mysqli->query($q);
}$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);

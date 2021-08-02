<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");


if($_POST["project_id"]) {
	$q='delete from projects where project_id='.$_POST["project_id"];
	$mysqli->query($q);
}$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);

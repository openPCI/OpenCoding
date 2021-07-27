<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");

if($_POST["member"]) {
	$q='update tasks set `group_id`='.$_POST["parent"].' where task_id='.$_POST["member"];
	$mysqli->query($q);
}
$res["log"]=$log;


echo json_encode($res);

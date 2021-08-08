<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");

if($_POST["member"]) {
	$q='update tasks set `group_id`=0 where task_id='.$_POST["member"];
	$mysqli->query($q);
}
$res["taskContent"]=$_POST["taskContent"];
$res["test_id"]=$_POST["test_id"];
$res["log"]=$log;


echo json_encode($res);
